<?php

namespace Modules\Product\App\Interfaces;

interface ProductInterface
{
    public function index();

    public function store($request);

    public function show($user);

    public function update($request, $user);

    public function delete($user);
}
