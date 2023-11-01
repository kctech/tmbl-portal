@extends('layouts.pdf.default')

@section('logo', imgBase64('img/'.$record->user->account->viewset.'/'.$record->user->account->logo_pdf))
@section('title', 'Gateway Mortgages Ltd')
@section('pagetitle', 'Business Terms')

@section('content')

<div class="no-break">
	<h2 class="h1">Terms of Business</h2>
	<h6>Our Terms of Business document outlines the services we will provide. It is an important document, so please ensure that you take time to read it carefully.</h6>
	<p>If there is anything in this document that you are unsure about, please don’t hesitate to get in touch with your adviser</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2 class="h1">Who we are</h2>
	<p>Gateway Mortgages Ltd is an appointed representative of Mortgage Advice Bureau Limited who are authorised and regulated by the Financial Conduct Authority (FCA).</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2 class="h1">Mortgage Planning</h2>
	<h2>Mortgages we offer</h2>
	<ul>
		<li>We offer a comprehensive range of first charge mortgages from across the market, but not deals that you can only obtain by going direct to a lender.</li>
		<li>If we are recommending a Buy to Let mortgage for you, it is important to understand that not all Buy to Let mortgages are regulated by the FCA. We will confirm to you if any product we are recommending is not regulated.</li>
	</ul>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Alternative options</h2>
	<ul>
		<li>We do not offer advice on second charge mortgages or loans, Commercial Lending or unsecured lending. Where customers have a need for these types of loans we will refer to a specialist broker.</li>
		<li>Where we identify other products and services that could be of interest to you we will make you aware of them.</li>
	</ul>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Protection Planning</h2>
	@if($record->service == 'MP' || $record->service == 'P')
		<ul>
			<li> We offer Life, Critical/Serious Illness, Income Protection, Accident/Sickness &amp; Unemployment and Buildings &amp; Contents products from a panel of leading insurers. Our insurer panel consists of;
				<ul>
					<li>Aviva</li>
					<li>Legal &amp; General</li>
					<li>Paymentshield</li>
					<li>Royal London</li>
					<li>Uinsure</li>
					<li>Vitality life insurance</li>
					<li>Liverpool Victoria</li>
				</ul>
			</li>
			<li>We will advise you and make a recommendation, after your needs have been assessed.</li>
		</ul>
	@else
		<p>We will refer you to a Protection specialist for advice on your protection needs.</p>
	@endif

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>
		Fees &amp; Costs &ndash;
		<br />Residential &amp; Buy to Let Mortgages
	</h2>
	<p>We charge a fee for arranging your mortgage. Our actual fees and charges will be explained before we do any work, and we will explain payment options to you.</p>
	<p>Fees vary according to individual circumstances, and we agree our fees with you before we undertake any chargeable work. This fee is for advice, recommendation, research and application of the loan.</p>
	<p>This can be up to 1% of the mortgage amount and is payable on application at the earliest.</p>
	<h5><strong><em>Example</em></strong> <em>(for illustrative purposes only):</em></h5>
	<p><em>If your mortgage is for &pound;100,000, we may charge up to &pound;1,000 in total, which equates to 1% of the loan.</em></p>
	<span class="text-muted">
		<p>There may be additional costs and charges relating to the mortgage product we recommend. You will receive a Mortgage Illustration when considering a particular mortgage and product, which will detail any fees relating to it.</p>
		<p>We will receive commission from the mortgage lender (in addition to the fees you pay).</p>
		<p>If you would like to see a list of the commission rates payable by the mortgage lenders we have access to, please ask your adviser. The precise amount paid by the lender recommended to you will be detailed in the Mortgage Illustration which we will give to you.</p>
	</span>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Refund of Mortgage Fees</h2>
	<p>We do not offer a refund of Mortgage Fees.</p>

	<hr class="my-4" />
</div>

@if($record->service == 'MP' || $record->service == 'P')
	<div class="no-break">
		<h2>Fees &amp; Costs – Protection Planning</h2>
		<p>We arrange policies with the insurers on your behalf. You do not pay us a fee for doing this. We will receive commission from the insurers which is a percentage of the total annual premium.</p>

		<hr class="my-4" />
	</div>
@endif

<div class="no-break">
	<h2>The service we offer</h2>
	<p>We are committed to delivering the highest standard of service and customer care throughout your financial lifecycle.</p>

	<hr class="my-4" />
</div>

@if($record->service != 'P')
<div class="no-break">
	<h2>Mortgage Advice</h2>
	<p>We offer full advice and recommendation to you. This means we will:</p>
	<ul>
		<li>Explain all parts of the mortgage journey to you and the value we will add to your journey.</li>
		<li>Get to know you, and understand your needs, preferences and circumstances.</li>
		<li>Identify your lifestyle and financial expectations over the life of the mortgage.</li>
		<li>Explore your needs fully and raise any contradictions which we will discuss to clearly establish your needs and priorities.</li>
		<li>Advise and recommend products that are appropriate for you based on the above.</li>
	</ul>
	<p>Any recommendations made will be supported by:</p>
	<ul>
		<li>An explanation of the implications of the particular mortgage features, both now, in the future and with interest rate movement.</li>
		<li>An explanation of the costs associated with the mortgage and the impact of adding any of these to the mortgage.</li>
		<li>An explanation of the next steps and timescales including any administration needed. For example, underwriting / valuation.</li>
		<li>Advice on how and who you need to contact for help or support throughout the solution lifecycle</li>
		<li>A report confirming why the recommendation has been made.</li>
		<li>Illustrations and other relevant information.</li>
	</ul>

	<hr class="my-4" />
</div>
@endif

@if($record->service == 'MP' || $record->service == 'P')
	<div class="no-break">
		<h2>Protection Advice</h2>
		<p>We believe that advice is required when considering protection. There are many different ways of ensuring you are correctly protected. The expert professional guidance we give you will help you through this process.</p>
		<ul>
			<li>The first step is to understand your own personal circumstances.</li>
			<li>Any solutions designed will need to fit your individual needs.</li>
			<li>We will look at what current provisions you have and take them into account. This includes protection you may have personally taken out and protection you may have at work.</li>
			<li>We will then help you arrange the right quality of cover for your needs within your budget. Your budget is very important to us and we ensure that whatever is recommended is affordable to you.</li>
		</ul>
		<p>We believe in bespoke solutions tailored to your own personal circumstances, and we will provide you with a full report on why any recommendation has been made.</p>
 		<p>We will discuss the appropriate timings to review your arrangements, because your circumstances may change and products and prices on offer can change as well.</p>

		<hr class="my-4" />
	</div>
@endif

<div class="no-break">
	<h2>Duty of Disclosure</h2>
	<p>Any advice provided is based on your personal financial circumstances and objectives. It is important that the information you provide is both accurate and honest, and a true reflection of your circumstances.</p>
	<p>It is your responsibility to provide information in this way to a provider or organisation that provides products and services recommended for you.</p>
	<p>Failure to disclose relevant information, or change of circumstances, to a provider may result in your chosen plan or product being invalidated.</p>
	<p>We strongly recommend that any information provided is checked thoroughly prior to submission.</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2 class="h1">Other important information</h2>
	<h2>Who regulates us</h2>
	<p>Gateway Mortgages Ltd is an appointed representative of Mortgage Advice Bureau Limited, who are authorised and regulated by the Financial Conduct Authority (FCA).</p>

	<ul>
		<li>Mortgage Advice Bureau Limited, Capital House, Pride Place, Derby, Derbyshire, DE24 8QR for mortgages.</li>
		<li>Mortgage Advice Bureau (Derby) Limited, Capital House, Pride Place, Derby, Derbyshire, DE24 8QR for general insurance.</li>
	</ul>

	<p>The FCA regulates financial services.</p>
	<ul>
		<li>Mortgage Advice Bureau Limited&rsquo;s FCA registration number is 455545, and its permitted business is advising on and arranging mortgages and general insurance.</li>
		<li>Mortgage Advice Bureau (Derby) Limited&rsquo;s FCA registration number is 466154, and its permitted business is advising on and arranging mortgages and general insurance.</li>
	</ul>

	<p>Mortgage Advice Bureau is authorised and regulated by the FCA in respect of consumer credit activities. You can check this on the FCA&rsquo;s Register by visiting <a href="https://register.fca.org.uk/">register.fca.org.uk</a> or by contacting them on <strong>0845 606 1234</strong>.</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>If you are not happy with our service</h2>
	<p>Our number one priority is to provide customers with the highest level of service. However, we know that sometimes things don&rsquo;t go as planned.
		Customer feedback helps us understand where things have gone wrong and gives us the opportunity to put
		them right. It also helps us to understand where we need to improve our products and services.</p>
	<p>If you have a complaint about your adviser; mortgage advice you have received from your adviser, please contact us:</p>
	<ul>
		<li>In writing: Resolutions Department (Complaints), Mortgage Advice Bureau Limited, Capital House, Pride Place, Derby, Derbyshire, DE24 8QR</li>
		<li>By phone: 01332 200020</li>
		<li>Email: <a href="mailto:complaints@mab.org.uk">complaints@mab.org.uk</a></li>
	</ul>
	<p>If you cannot settle your complaint with us, you may be entitled to refer it to the Financial Ombudsman Service (FOS).</p>
	<p>Further information on the services provided by the FOS can be found on their website <a href="http://www.financial-ombudsman.org.uk/">financial-ombudsman.org.uk </a>or alternatively:</p>
	<ul>
		<li>In writing: The Financial Ombudsman Service, Exchange Tower, E14 9SR</li>
		<li>By phone: 0800 023 4567 or 0300 123 9123</li>
		<li>Email: <a href="mailto:complaint.info@financial-ombudsman.org.uk">info@financial-ombudsman.org.uk</a></li>
	</ul>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Personal Data Complaint</h2>
	<p>If your complaint is related to how your personal data has been processed and you are not satisfied with the response from us, you have the right to complain to the Information Commissioners Office (ICO) who is the regulator for data protection in the
		United Kingdom.</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Addressing Financial Crime</h2>
	<p>The FCA, as part of our responsibility to ensure the integrity of the UK Financial Services Market requires us to have adequate systems and controls in place to prevent the furtherance of Financial Crime.</p>
	<p>All transactions relating to our services provided are covered by, and adhere to, the Money Laundering regulations and Proceeds of Crime Act.</p>
	<p>Our responsibilities include, but are not limited to, verifying the identity and address of our customers. Identity verification checks may include electronic searches through the Electronic Identification Verification Service, electoral roll and use
		of credit reference agencies.</p>
	<p>We will also require proof of your income and expenditure, and source of any deposit funds to further satisfy these requirements.</p>
	<p>Your adviser will confirm what documentation is required.</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>Financial Services Compensations Scheme (FSCS)</h2>
	<p>We are covered by the FSCS. You may be entitled to compensation from the scheme if we cannot meet our obligations. This depends on the type of business and the circumstances of the claim.</p>
	{{--<!--<ul>
		<li>Mortgage advising and arranging is covered for 100% of the claim up to &pound;85,000, so the maximum compensation is &pound;85,000.</li>
		<li>Insurance advising and arranging is covered for 90% of the claim, with no upper limit.</li>
	</ul>-->--}}
	<p> Further information about compensation scheme arrangements is available from the FSCS <a href="https://www.fscs.org.uk">www.fscs.org.uk</a>.</p>

	<hr class="my-4" />
</div>

<div class="no-break">
	<h2>The Law that we operate under</h2>
	<p>All of our agreements provided are governed and construed in accordance with the laws of England and Wales, in relation to any dispute. For your protection you agree to submit to the non-exclusive jurisdiction of the English courts.</p>
</div>

@endsection