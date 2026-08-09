<?php

/* @var $this yii\web\View */

$this->title = 'License';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content license-page style_external_links">
    <header class="license-page__intro">
        <h1>Yii licenses</h1>
        <p>Yii can be used freely in open-source and commercial projects. The framework and its documentation use separate licenses.</p>
        <nav class="license-page__nav" aria-label="License sections">
            <a href="#framework">Framework</a>
            <a href="#documentation">Documentation</a>
        </nav>
    </header>

    <section class="license-section" id="framework" aria-labelledby="framework-title">
        <header class="license-section__header">
            <div>
                <p class="license-eyebrow">3-clause BSD</p>
                <h2 id="framework-title">Yii Framework</h2>
            </div>
            <p>A permissive license that allows use, modification, and distribution with minimal conditions.</p>
        </header>

        <div class="license-permissions">
            <section class="license-permission license-permission--must">
                <h3><span aria-hidden="true">!</span> You must</h3>
                <ul>
                    <li>Retain the original copyright notice, conditions, and disclaimer.</li>
                </ul>
            </section>

            <section class="license-permission license-permission--can">
                <h3><span aria-hidden="true">✓</span> You can</h3>
                <ul>
                    <li>Use Yii commercially.</li>
                    <li>Modify and distribute it.</li>
                    <li>Sublicense derivative work.</li>
                    <li>Offer a warranty for your own distribution.</li>
                </ul>
            </section>

            <section class="license-permission license-permission--cannot">
                <h3><span aria-hidden="true">×</span> You can’t</h3>
                <ul>
                    <li>Use contributor names, logos, or trademarks to endorse your product without permission.</li>
                    <li>Hold the copyright owners or contributors liable for damages.</li>
                </ul>
            </section>
        </div>

        <div class="license-legal">
            <header>
                <h3>Full license text</h3>
                <p>Copyright &copy; 2008 by <a href="https://www.yiiframework.com/">Yii Software</a><br>All rights reserved.</p>
            </header>

            <div class="license-legal__body">
                <p>Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:</p>
                <ol>
                    <li>Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.</li>
                    <li>Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.</li>
                    <li>Neither the name of Yii Software nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.</li>
                </ol>
                <p class="license-legal__disclaimer">THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</p>
            </div>
        </div>
    </section>

    <section class="license-section license-section--documentation" id="documentation" aria-labelledby="documentation-title">
        <header class="license-section__header">
            <div>
                <p class="license-eyebrow">GNU FDL</p>
                <h2 id="documentation-title">Documentation</h2>
            </div>
            <p>Official and user-contributed Yii documentation is published under the <a href="https://www.gnu.org/copyleft/fdl.html" rel="nofollow">GNU Free Documentation License 1.2 or later</a>.</p>
        </header>

        <div class="license-documentation-grid">
            <div class="license-permission license-permission--must">
                <h3><span aria-hidden="true">!</span> You must</h3>
                <p>Preserve the same freedoms and acknowledge the authors by linking to the original article.</p>
            </div>
            <div class="license-permission license-permission--can">
                <h3><span aria-hidden="true">✓</span> You can</h3>
                <p>Copy, modify, and redistribute the documentation.</p>
            </div>
        </div>

        <p class="license-documentation-note">Permission is granted with no invariant sections, no front-cover texts, and no back-cover texts.</p>
    </section>
</main>
