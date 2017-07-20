<?php
namespace models;

use core;
use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 7/11/17
 */
class Domain extends core\Model {

    const TABLE = 'domains';

    public $id;
    public $name;
    public $resolvedUrl;

    /** @var \stdClass */
    public $scrape;

    /** @var \stdClass */
    public $moz;

    /** @var \stdClass */
    public $sitemap;

    /**
     * @return $this
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024) throw new Exception('Invalid id', 400);
        if (mb_strlen($this->name) > 1024 || !mb_strlen($this->name)) throw new Exception('Invalid name', 400);

        return $this;
    }

    /**
     * Adds an array of domains to the database
     * @param Domain[]
     */
    public static function addMulti(array $domainNames) {
        $domainNames = array_values(array_map('core\\Format::domain', $domainNames));
        $existingDomains = [];
        foreach (self::findMulti(['name' => ['$in' => $domainNames]]) as $domain)
            $existingDomains[] = $domain->name;

        $newDomains = array_diff($domainNames, $existingDomains);
        $newDomains = array_filter($newDomains, 'trim');

        core\Debug::info(__METHOD__ . ' Adding ' . count($newDomains) . ' domains');

        foreach ($newDomains as $newDomainName) {
            self::new(['name' => $newDomainName])->save();
        }
    }

}