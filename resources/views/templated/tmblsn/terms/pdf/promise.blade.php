@extends('layouts.pdf.default')

@section('logo', imgBase64('img/'.$record->user->account->viewset.'/'.$record->user->account->logo_pdf))
@section('title', 'Our Promise')

@section('content')

<div class="no-break">
    <h3>Our Promise to You</h3>
    <h6>This document establishes the fees and our terms &amp; conditions that are relevant to the work we will do for you.</h6>
</div>

<div class="no-break">
    <h2>Specifically, this document confirms:</h2>
    <ul>
        <li>How we manage your personal data in accordance with the Client Privacy Notice</li>
        <li>The documents we have provided during our initial and subsequent contact with you</li>
        <li>How and when we will be paid if we arrange a mortgage for you</li>
        <li>How and when you would want future contact from us to provide ongoing service</li>
    </ul>

    <p>Please take time to read through these terms fully and take the opportunity of asking any questions to confirm your understanding, before signing this form.</p>

    <hr class="my-4" />
</div>

<div class="no-break">
    <h2>Your Personal Data</h2>
    <p>By signing this declaration, you agree that:</p>

    <ul>
        <li>Your consent has been given for this appointment and any future meetings</li>
        <li>Personal data we hold about you may be processed by Mortgage Advice Bureau, and where necessary
            shared with third parties, such as product providers, for the purpose of processing your application,
            assessing the risk to grant credit and for regulatory purposes</li>
    </ul>

    <hr class="my-4" />
</div>

<div class="no-break">
    <h2>Guides to Help you</h2>
    <p><strong>NB:</strong> You will only be provided with the Information Guide(s) that may be relevant to you.</p>

    <table class="table table-striped table-bordered bg-white">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Name of document</th>
                <th scope="col">Date / Method Provided</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Client Privacy Notice
                </td>
                <td>
                    {{\Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i')}} by email
                </td>
            </tr>
            <tr>
                <td>
                    Terms of Business
                </td>
                <td>
                    {{\Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i')}} by email
                </td>
            </tr>
        </tbody>
    </table>

	<hr class="my-4" />
</div>

<div class="no-break">
    <h2>Agreement to ongoing service and Re-engagement</h2>
    <p>We would also like to keep in touch to review your mortgage, insurance needs and current arrangements,
        in particular when your mortgage product is nearing expiry. This is important as, for example, it will
        be an opportunity to check that you are not paying more than you need to and whether your existing arrangements
        are still appropriate as your circumstances and needs change.</p>

    <p>You may withdraw from these arrangements at any time by contacting us by the
        following e-mail or in writing at the address shown below.</p>

    <table class="table table-bordered bg-white">
        <tbody>
            <tr>
                <td class="bg-dark text-white">
                    Email
                </td>
                <td>
                    {{ $record->user->email }}
                </td>
            </tr>
            <tr>
                <td class="bg-dark text-white">
                    Address
                </td>
                <td>
                    8 Steel Close, Eaton Socon, St Neots, PE19 8TT
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="my-4" />
</div>

<div class="no-break">
    <h2>Fees and Costs explained</h2>
    <table class="table table-bordered bg-white">
        <tbody>
            <tr>
                <td class="bg-dark text-white">
                    Brief outline of service provided
                </td>
                <td>
                    {{ $record->description }}
                </td>
            </tr>
            @if($record->service != 'P')
                <tr>
                    <td class="bg-dark text-white">
                        Method of payment for arranging this mortgage
                    </td>
                    <td>
                        @if($record->amount == 0)
                            Commission from lender
                        @else
                            Both commission from lender and fee paid by you
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="bg-dark text-white">
                        Fee paid by you for arranging this mortgage
                    </td>
                    <td>
                        @if($record->amount == 0)
                            No fee
                        @else
                            @if($record->type == "Percentage")
                                {{ $record->amount }}%
                            @else
                                &pound;{{ number_format($record->amount, 2, '.', '') }}
                            @endif
                            @if($record->timing == "Application")
                                paid on application of the mortgage
                            @elseif($record->timing == "Offer")
                                paid on receipt of the mortgage offer
                            @elseif($record->timing == "Completion")
                                paid on completion of the mortgage (i.e. when the funds are drawn down) 
                            @endif
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <td class="bg-dark text-white">
                    Fee paid by you for any Protection Advice
                </td>
                <td>
                    No fee is charged to you
                </td>
            </tr>
        </tbody>
    </table>
</div>


<div class="no-break">
    <h2>Client Agreements</h2>

    <table class="table table-bordered bg-white">
        <tbody>
            <tr>
                <td class="bg-dark text-white w-50">
                    Client
                </td>
                <td class="w-50">
                    {{ $record->client->first_name }} {{ $record->client->last_name }}
                </td>
            </tr>
            {{--<tr>
                <td class="bg-dark text-white">
                    I agree to the privacy notice.
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>--}}
            <tr>
                <td class="bg-dark text-white">
                    <ul>
                        <li>I / We confirm that I / we have received a copy of the Terms of Business document, the Client Privacy Notice, any relevant guides, and agree to the terms therein</li>
                        <li>I / We give you authority to act on my/our behalf as per the terms & conditions defined.</li>
                        @if($record->amount > 0)
                            <li>I authorise {{$record->user->account->name }} to send instructions to the financial institution that issued my card to take payments from my card account,
                            in accordance with the terms of my agreement with you.</li>
                        @endif
                    </ul>
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>
            <tr>
                <td class="bg-dark text-white">
                    Signature
                </td>
                <td>
                    Signed:
                    <br /><br />
                    Dated:
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="my-4" />
</div>

<div class="no-break">
    <h2>Communication &amp; Marketing Preferences</h2>

    <table class="table table-striped table-bordered bg-white">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Medium</th>
                <th scope="col">Communications Preference</th>
                <th scope="col">Marketing Preference</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td class="bg-dark text-white">
                    Phone
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    Face-to-face
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    SMS
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    Email
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    3<sup>rd</sup> Party Intermediary
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>
            
            <tr>
                <td class="bg-dark text-white">
                    Other
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    Post
                </td>
                <td>
                    N/A
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

            <tr>
                <td class="bg-dark text-white">
                    Automated Call
                </td>
                <td>
                    N/A
                </td>
                <td>
                    Yes &nbsp; / &nbsp; No
                </td>
            </tr>

        </tbody>
    </table>

    <hr class="my-4" />
</div>
@endsection