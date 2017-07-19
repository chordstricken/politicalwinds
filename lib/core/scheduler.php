<?php

namespace core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
abstract class Scheduler {
    use Singleton;

    public abstract function schedule();

}