<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Request;


interface EntityFactoryInterface
{
	public function createOrUpdate(Request $request, $entity = null);
}
