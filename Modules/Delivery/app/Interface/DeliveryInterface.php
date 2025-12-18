<?php

namespace Modules\App\Interface;

interface DeliveryInterface
{
    public function index();

    public function show($delivery);

    public function store($request);

    public function update($request,$delivery);

    public function delete($delivery);

}