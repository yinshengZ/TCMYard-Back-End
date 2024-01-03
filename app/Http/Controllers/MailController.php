<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

use App\Mail\InvoiceGenerated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function test_email()
    {
        Mail::to('dongsmbm@gmail.com')->send(new InvoiceGenerated);
    }
}
