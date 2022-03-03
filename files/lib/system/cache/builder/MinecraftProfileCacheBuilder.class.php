<?php
namespace wcf\system\cache\builder;

class MinecraftProfileCacheBuilder extends AbstractCacheBuilder {

    /**
     * Max requests in $reset_time
     * @var int
     */
    protected $max_requests = 600;

    /**
     * 10 min = 600 sec
     * @var int
     */ 
    protected $reset_time = 600;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        if (isset($parameters['count']) && is_int($parameters['count'])) {
            $data['count'] = $parameters['count'];
        } else {
            $data['count'] = 0;
        }
        if (isset($parameters['time']) && is_int($parameters['time'])) {
            $data['time'] = $parameters['time'];
        } else {
            $data['time'] = TIME_NOW;
        }

        return $data;
    }

    /**
     * Checks if Mojang-API can be used.
     *
     * @return bool true if a a new try can be startet.
     *              Automatically adds a try to the counter and resets time possable.
     *              Otherwise false.
     */
    public function try()
    {
        $data = $this->getData(['count', 'time']);
        $count = $data['count'];
        $time = $data['time'];
        if ($count <= $this->max_requests) {
            $this->rebuild([
                'count' => $count++,
                'time' => $time
            ]);
            return true;
        } else if ((TIME_NOW - $time) > $this->reset_time) {
            $this->reset();
            return true;
        } else {
            return false;
        }
    }
}