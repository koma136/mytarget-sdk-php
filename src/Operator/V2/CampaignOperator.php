<?php

namespace Koma136\MyTarget\Operator\V2;

use Koma136\MyTarget\Domain\V2\Campaign\Campaign;
use Koma136\MyTarget\Domain\V2\Campaign\Campaigns;
use Koma136\MyTarget\Domain\V2\Campaign\MutateCampaign;
use Koma136\MyTarget\Domain\V2\Enum\Status;
use Koma136\MyTarget\Mapper\Mapper;
use Koma136\MyTarget\Operator\V2\Fields\CampaignFields;
use Koma136\MyTarget\Client;
use Koma136\MyTarget\Context;
use Koma136\MyTarget as f;
use GuzzleHttp\Psr7 as psr;

class CampaignOperator
{
    const LIMIT_CREATE = "campaign-create";
    const LIMIT_EDIT = "campaign-edit";
    const LIMIT_FIND = "campaigns-find";

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    public function __construct(Client $client, Mapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    /**
     * @param MutateCampaign $campaign
     * @param Context|null $context
     *
     * @return Campaign
     */
    public function create(MutateCampaign $campaign, Context $context = null)
    {
        $context = Context::withLimitBy($context, self::LIMIT_CREATE);
        $rawCampaign = $this->mapper->snapshot($campaign);

        $json = $this->client->post("/api/v2/campaigns.json", null, $rawCampaign, $context);

        return $this->mapper->hydrateNew(Campaign::class, $json);
    }

    /**
     * @param int $id
     * @param MutateCampaign $campaign
     * @param Context|null $context
     * @deprecated Use edit() instead. All editing actions will be consistently named across all Operators as edit*
     *
     * @return Campaign
     */
    public function update($id, MutateCampaign $campaign, Context $context = null)
    {
        return $this->edit($id, $campaign, $context);
    }

    /**
     * @param int $id
     * @param MutateCampaign $campaign
     * @param Context|null $context
     *
     * @return Campaign
     */
    public function edit($id, MutateCampaign $campaign, Context $context = null)
    {
        $context = Context::withLimitBy($context, self::LIMIT_EDIT);
        $rawCampaign = $this->mapper->snapshot($campaign);

        $json = $this->client->post(sprintf("/api/v2/campaigns/%d.json", $id), null, $rawCampaign, $context);

        return $this->mapper->hydrateNew(Campaign::class, $json);
    }

    /**
     * @param CampaignFields|null $fields
     * @param array|null $withStatuses
     * @param Context|null $context
     * @return Campaigns
     */
    public function all(CampaignFields $fields = null, array $withStatuses = null, Context $context = null)
    {
        $context = Context::withLimitBy($context, self::LIMIT_FIND);
        $fields = $fields ?: CampaignFields::create();
        $query = ["fields" => $this->mapFields($fields->getFields())];

        if ($withStatuses && null !== ($status = Status::inApiFormat($withStatuses))) {
            $query["_status"] = $status;
        }

        $json = $this->client->get("/api/v2/campaigns.json", $query, $context);

        return $this->mapper->hydrateNew(Campaigns::class, $json);
    }

    /**
     * Returns campaign with given $id or null if it doesn't exist
     *
     * @param int $id
     * @param CampaignFields|null $fields
     * @param Context|null $context
     *
     * @return CampaignStat|null
     */
    public function find($id, CampaignFields $fields = null, Context $context = null)
    {
        $campaigns = $this->findAll([$id], $fields, null, $context);

        return $campaigns ? reset($campaigns) : null;
    }

    /**
     * Returns all campaigns with given $ids and statuses
     *
     * @param int[] $ids
     * @param CampaignFields|null $fields
     * @param Status[]|null $withStatuses
     * @param Context|null $context
     *
     * @return CampaignStat[]
     */
    public function findAll(array $ids, CampaignFields $fields = null, array $withStatuses = null, Context $context = null)
    {
        $context = Context::withLimitBy($context, self::LIMIT_FIND);

        $fields = $fields ?: CampaignFields::create();
        $query = ["fields" => $this->mapFields($fields->getFields())];
        if ($fields->hasField(CampaignFields::FIELD_BANNERS)) {
            $query["with_banners"] = "1";
        }
        if ($withStatuses && ($status = Status::inApiFormat($withStatuses))) {
            $query["status"] = $status;
        }

        $path = sprintf("/api/v2/campaigns/%s.json", implode(";", $ids));
        $json = $this->client->get($path, $query, $context);
        $json = f\objects_array_fixup($json, count($ids));;

        return array_map(function ($json) {
            return $this->mapper->hydrateNew(CampaignStat::class, $json);
        }, $json);
    }

    /**
     * TODO to be changed
     *
     * @param array $fields
     * @return string
     */
    private function mapFields(array $fields)
    {
        $fields = array_filter($fields, function ($field) {
            return $field !== CampaignFields::FIELD_BANNERS;
        });

        $fields = array_map(function ($field) {
            return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $field));
        }, $fields);

        return implode(",", $fields);
    }
}
