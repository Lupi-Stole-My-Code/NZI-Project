<?php
/**
 * Created by PhpStorm.
 * User: grzes
 * Date: 4/30/2016
 * Time: 11:36 PM
 */

namespace common\components;


use MyCLabs\Enum\Enum;

class EVisibility extends Enum
{
	const friends = "friends";
	const visible = "visible";
	const hidden = "hidden";
}