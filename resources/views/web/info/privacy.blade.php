<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

@extends('layouts.default', [
    'title' => 'Privacy Policy'
])

@section('css')
    <style>
    .card .content {
        padding: 0 10px;
    }

    h5 {
        margin: 5px;
    }
    </style>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
    <div class="large-text margin-bottom">Privacy Policy</div>
    <div class="card">
    <div class="top blue">General</div>
    <div class="content">
    <p>This privacy policy regards the website <a href="/">{{ request()->getHost() }}</a> and all associated subdomains.</p>
    <p>Any questions regarding this policy may be directed to <a href="mailto:{{ config('site.emails.support') }}">{{ config('site.emails.support') }}</a>.</p>
    <p>Staff reserve the right to change or adapt this policy at any time for any reason.<br>
    We care for your privacy; if you question our practices, please email <a href="mailto:{{ config('site.emails.support') }}">{{ config('site.emails.support') }}</a>.</p>
    </div>
    </div>
    <div class="card">
    <div class="top blue">Requested Information</div>
    <div class="content">
    <p>Users may view our content anonymously as a guest, though may be restricted from some features. This is due to the lack of control we have over moderating an anonymous account.</p>
    <p>We do not share any information with third-parties for non statistical purposes.</p>
    <p>It is required that users choose a safe password that is not easy to guess or crack. As well as this, all passwords are stored in an encrypted form and cannot be accessed by anyone.</p>
    <p>Upon signing up to Brick Hill, we require you supply a valid email address. This both verifies your account and enables more user security.</p>
    </div>
    </div>
    <div class="card">
    <div class="top blue">Stored Information</div>
    <div class="content">
    <p>Non-personally identifiable information is automatically collected when visiting <a href="/">{{ request()->getHost() }}</a>.</p>
    <p>Brick Hill may use scripts, cookies and other methods for monitering activity on our website in order to better administrate and control certain actions.</p>
    <p>Your IP address, browser type and date/time may be stored, though do not obtain any personally indentifiable information.</p>
    <p>All passwords are encrypted, and hence the original password cannot be retrieved by any parties.</p>
    <p>Brick Hill will not attempt to obtain your location through a device or application. If your location is ever requested, it will not be shared with third-parties and would be optional.</p>
    <p>For security purposes, all actions performed by users are logged and may be used for support. These logs may consist of your IP address, requested pages, browser type, date/time, and operating system.</p>
    </div>
    </div>
    <div class="card">
    <div class="top blue">Shared Information</div>
    <div class="content">
    <p>Brick Hill will only share personal information in ways specified in this privacy policy; we do not sell any personal information to third-party companies.</p>
    <p>Brick Hill reserves the right to share personally identifiable information required by law to protect the rights of our website or to comply with a court order.</p>
    <p>To the extent permitted by applicable law, Brick Hill may also share personally identifiable information with law enforcement or other agencies if the concealment of such material may risk the safety of both the party involved or our website.</p>
    </div>
    </div>
    <div class="card">
    <div class="top blue">Updating Information</div>
    <div class="content">
    <p>You may change or request the removal of any personally identifiable information stored by Brick Hill either on your account settings page or by emailing <a href="mailto:{{ config('site.emails.support') }}">{{ config('site.emails.support') }}</a></p>
    <p>Brick Hill will hold all information associated with your account for as long as it is required in providing you our services.<br>
    Your information will be used to settle disputes when needed for legal obligations and to enforce our policies.</p>
    </div>
    </div>
    <h5>Effective Date: 11/04/2017</h5>
    <h5>Last Updated: 11/04/2017</h5>
    </div>
@endsection
