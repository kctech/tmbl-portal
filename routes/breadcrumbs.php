<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});

// Leads
Breadcrumbs::for('leads', function (BreadcrumbTrail $trail) {
    $trail->push('Leads', route('leads.index'));
});

// Home > GDPR
Breadcrumbs::for('gdpr-consent', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('GDPR Consents', route('gdpr-consent.index'));
});

Breadcrumbs::for('btl-consent', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('BTL Confirmations', route('btl-consent.index'));
});

Breadcrumbs::for('sdlt-consent', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('SDLT Disclaimers', route('sdlt-consent.index'));
});

Breadcrumbs::for('transfer-request', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Transfer Requests', route('transfer-request.index'));
});

Breadcrumbs::for('terms-consent', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Business Terms Consents', route('terms-consent.index'));
});

Breadcrumbs::for('clients', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Clients', route('clients.index'));
});

Breadcrumbs::for('users', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Users', route('users.index'));
});

Breadcrumbs::for('quote', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Quotes', route('quote.index'));
});

Breadcrumbs::for('eligibility-statements', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Eligibility Statements', route('eligibility-statements.index'));
});
