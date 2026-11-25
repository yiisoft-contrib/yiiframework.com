<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Vulnerability disclosure and CVE policy';
$this->params['breadcrumbs'][] = ['label' => 'Report a Security Issue', 'url' => ['site/security']];
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content report-issue-page style_external_links">
    <header class="report-issue-page__intro">
        <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Html::a('Report a security issue privately', ['site/security']) ?> to the maintainers of the affected project.</p>
    </header>

    <section class="report-issue-section" id="scope">
        <header><h2>Scope</h2></header>
        <div class="report-issue-section__body">
            <p>Yii’s proposed CVE assignment scope covers the Yii framework and official open-source packages
                maintained by the Yii project. Report flaws in third-party extensions and dependencies to their
                maintainers. If a report belongs to another CVE Numbering Authority (CNA), we will help direct
                it to that authority.</p>
            <p>Use the website option on the reporting page for issues with the live site or its accounts.
                If the issue also affects a distributed Yii package, identify that package in your report.</p>
        </div>
    </section>

    <section class="report-issue-section" id="cve-assignment">
        <header><h2>Request a CVE ID</h2></header>
        <div class="report-issue-section__body">
            <p>Use the affected repository’s private reporting channel to request a CVE ID or ask whether one
                already exists. For a public vulnerability, include a link to the disclosure. We review the security impact, identify the affected product and versions, and check
                for an existing assignment. We will explain our decision in the report.</p>
            <p>Until Yii is authorized to assign CVE IDs, we coordinate eligible requests with an appropriate CNA.
                Once authorized, Yii will assign CVE IDs for distinct vulnerabilities within its approved scope
                that are public or expected to be disclosed, following the
                <a href="https://www.cve.org/ResourcesSupport/AllResources/CNARules">CNA Operational Rules</a>.
                For example, a flaw in shared code can affect several packages under one CVE ID.
                Independently fixable vulnerabilities generally receive separate IDs.</p>
        </div>
    </section>

    <section class="report-issue-section" id="disclosure">
        <header><h2>Disclosure and advisories</h2></header>
        <div class="report-issue-section__body">
            <p>Maintainers discuss the findings with the reporter privately and coordinate a disclosure date,
                taking account of available fixes, mitigations and any public disclosure. An advisory can be
                published before a fix is available.</p>
            <p>We publish advisories on the affected repository’s public Security Advisories page, including
                the CVE ID when assigned, affected versions, impact and available fixes or mitigations.
                Read the <a href="https://github.com/yiisoft/yii/security/advisories">Yii 1.1 advisories</a>,
                <a href="https://github.com/yiisoft/yii2/security/advisories">Yii 2 advisories</a>
                or the advisories for the affected Yii3 package. Public advisories can be read without an account.</p>
            <p>The assigning CNA publishes the CVE Record with a link to the public advisory.</p>
        </div>
    </section>

    <section class="report-issue-section" id="disputes">
        <header><h2>Corrections and disputes</h2></header>
        <div class="report-issue-section__body">
            <p>To request a correction or ask us to reconsider a decision, follow up in the original report
                or use the affected repository’s private reporting channel. Include the advisory or CVE ID
                and the evidence for the change. We update our advisory and coordinate any corresponding
                CVE Record changes with the assigning CNA.</p>
            <p>For a formal dispute, send your reasons and evidence to the CNA responsible for the decision.
                If you disagree with its decision, you can escalate to its Root under the
                <a href="https://www.cve.org/Resources/General/Policies/CVE-Record-Dispute-Policy.pdf">CVE Record Dispute Policy</a>.</p>
        </div>
    </section>
</main>
