<h2>Specifically, this document confirms:</h2>
<ul>
    <li>How we manage your personal data in accordance with the Client Privacy Notice</li>
    <li>The documents we have provided during our initial and subsequent contact with you</li>
    <li>How and when we will be paid if we arrange a mortgage for you</li>
    <li>How and when you would want future contact from us to provide ongoing service</li>
</ul>

<p>Please take time to read through these terms fully and take the opportunity of asking any questions to confirm your understanding, before signing this form.</p>

<hr class="my-4" />

<h2>Your Personal Data</h2>
<p>By signing this declaration, you agree that:</p>

<ul>
    <li>Your consent has been given for this appointment and any future meetings</li>
    <li>Personal data we hold about you may be processed by Mortgage Advice Bureau, and where necessary
        shared with third parties, such as product providers, for the purpose of processing your application,
        assessing the risk to grant credit and for regulatory purposes</li>
</ul>

<hr class="my-4" />

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
                <span class="badge badge-primary">{{\Carbon\Carbon::parse($record->created_at)->diffForHumans()}}</span>
            </td>
        </tr>
        <tr>
            <td>
                Terms of Business
            </td>
            <td>
                {{\Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i')}} by email
                <span class="badge badge-primary">{{\Carbon\Carbon::parse($record->created_at)->diffForHumans()}}</span>
            </td>
        </tr>
    </tbody>
</table>