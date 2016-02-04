<?php

class ArbitraryInteropContainer implements \Interop\Container\ContainerInterface
{
    private $data = array(
        'aic_foo' => 'aic_foo_content',
    );

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Interop\Container\Exception\NotFoundException  No entry was found for this identifier.
     * @throws \Interop\Container\Exception\ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ArbitraryNotFoundException();
        }

        return $this->data[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->data[$id]);
    }
}

class ArbitraryNotFoundException extends \RuntimeException implements \Interop\Container\Exception\NotFoundException
{

}
