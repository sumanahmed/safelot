<?php
namespace App\Services\FormValidation;

use Illuminate\Http\Request;
interface IFormValidation
{
    public function rules();
    public function message();
    public function validate(Request $request, $id);
}