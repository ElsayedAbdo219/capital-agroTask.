<?php
namespace Modules\ReturnProduct\App\Interface;

interface ReturnProductInterface 
{

    public function index();

    public function show($id);

    public function store($request);

    public function update($request,$returnProduct);

    public function delete($id);
}