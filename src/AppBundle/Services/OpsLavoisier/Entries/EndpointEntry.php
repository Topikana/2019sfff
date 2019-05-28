<?php

namespace AppBundle\Services\OpsLavoisier\Entries;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 29/10/13
 */

use \Lavoisier\Entries\Entries;

class EndpointEntry extends Entries
{

    public function init()
    {
        $this->setDowntimeStatus();
        $this->setEndpointStatus();
    }

    public function hasDowntime()
    {
        return isset($this['DOWNTIME_PRIMARY_KEY']);
    }

    private function setDowntimeStatus()
    {

        if ($this->hasDowntime() === true) {
            if ($this['DOWNTIME_SEVERITY'] == 'OUTAGE') {
                $this['DOWNTIME_STATUS_LABEL'] = 'OUTAGE';
                $this['DOWNTIME_STATUS_CLASS'] = 'danger';
            } else {
                $this['DOWNTIME_STATUS_LABEL'] = 'WARNING';
                $this['DOWNTIME_STATUS_CLASS'] = 'warning';
            }
        }
    }

    private function setEndpointStatus()
    {

        if ($this->isInProduction() === null) {
            $this['STATUS_LABEL'] = 'not registered in GOC DB';
            $this['STATUS_NICK'] = 'NOT GOCDB';
            $this['STATUS_CLASS'] = 'danger';
        }

        if (!$this->isInProduction() && !$this->isNodeMonitored()) {
            $this['STATUS_LABEL'] = 'not in production and not monitored';
            $this['STATUS_NICK'] = 'NOT prod & NOT monit';
            $this['STATUS_CLASS'] = 'danger';
        }

        if ($this->isInProduction() && !$this->isNodeMonitored()) {
            $this['STATUS_LABEL'] = 'in production but not monitored';
            $this['STATUS_NICK'] = 'prod & NOT monit';
            $this['STATUS_CLASS'] = 'warning';
        }

        if (!$this->isInProduction() && $this->isNodeMonitored()) {
            $this['STATUS_LABEL'] = 'not in production but monitored';
            $this['STATUS_NICK'] = 'NOT prod & monit';
            $this['STATUS_CLASS'] = 'info';
        }

        if ($this->isInProduction() && $this->isNodeMonitored()) {
            $this['STATUS_LABEL'] = 'monitored and in production';
            $this['STATUS_NICK'] = "prod & monit";
            $this['STATUS_CLASS'] = 'success';
        }
    }

    public function isInProduction()
    {

        if (isset($this['IN_PRODUCTION'])) {
            return (($this['IN_PRODUCTION'] === 'Y') ? true : false);
        } else {
            return null;
        }
    }

    public function isNodeMonitored()
    {
        if (isset($this['NODE_MONITORED'])) {
            return (($this['NODE_MONITORED'] === 'Y') ? true : false);
        } else {
            return null;
        }
    }
}