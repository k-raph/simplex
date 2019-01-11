<?php

namespace Simplex\DataMapper\Relations;

class OneToMany
{
    protected $owner;

    protected $target;

    protected $ownerField;

    protected $targetField;

    public function __construct(string $owner, string $target, string $targetField, string $ownerField = 'id')
    {
        $this->owner = $owner;
        $this->target = $target;
        $this->targetField = $targetField;
        $this->ownerField = $ownerField;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getOwnerField(): string
    {
        return $this->ownerField;
    }

    public function getTargetField(): string
    {
        return $this->targetField;
    }
}
