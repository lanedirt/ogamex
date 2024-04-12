<?php

namespace OGame\Services\Objects\Models;

use OGame\Services\Objects\Models\Fields\GameObjectProperties;
use OGame\Services\Objects\Models\Fields\GameObjectRapidfire;

abstract class UnitObject extends GameObject
{
    /**
     * Optional class name of the object used in frontend which differs from the machine name.
     *
     * @var string
     */
    public string $class_name = '';

    /**
     * Objects that this object requires on with required level.
     *
     * @var array<GameObjectRapidfire>
     */
    public array $rapidfire;

    /**
     * Properties of the object.
     *
     * @var GameObjectProperties
     */
    public GameObjectProperties $properties;
}