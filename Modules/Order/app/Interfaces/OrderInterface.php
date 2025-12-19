<?php

namespace Modules\Order\App\Interfaces;

interface OrderInterface
{
    public function index();

    public function show($id);

    public function store($request);

    public function update($request,$order);

    public function delete($id);

}