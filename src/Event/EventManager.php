<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/06/2019
 * Time: 14:09
 */

namespace Simplex\Event;

use Sabre\Event\EventEmitter;

class EventManager extends EventEmitter implements EventManagerInterface
{

    /**
     * @inheritdoc
     */
    public function on($eventName, callable $callBack, $priority = 0)
    {
        parent::on($eventName, $callBack, $priority);
    }

}