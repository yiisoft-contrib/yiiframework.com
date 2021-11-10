<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'License';
?>
<div class="container site-header">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <h1><?= $this->title ?></h1>
            <h2>Framework and Documentation License</h2>
        </div>
        <div class="col-md-4 col-sm-4">
            <img class="background" src="<?= Yii::getAlias('@web/image/licenses/header.svg')?>" alt="">
        </div>
    </div>
</div>
<div class="container style_external_links">
    <div class="content license">
        <div class="row">
            <div class="text col-xs-12">
                <h2 id="framework">License of Yii Framework</h2>

                <p>The Yii framework is free software. It is released under the terms of the following BSD License.</p>

                <p>Copyright &copy; 2008-<?= date('Y'); ?> by Yii Software<br/>All rights reserved.</p>

                <p>Redistribution and use in source and binary forms, with or without modification, are permitted
                    provided that the following conditions are met:</p>

                <ul>
                    <li>Redistributions of source code must retain the above copyright notice, this list of conditions
                        and the following disclaimer.
                    </li>
                    <li>Redistributions in binary form must reproduce the above copyright notice, this list of
                        conditions and the following disclaimer in the documentation and/or other materials provided
                        with the distribution.
                    </li>
                    <li>Neither the name of Yii Software LLC nor the names of its contributors may be used to endorse or
                        promote products derived from this software without specific prior written permission.
                    </li>
                </ul>

                <p>THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR
                    IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
                    FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
                    CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
                    DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
                    DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
                    IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
                    THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</p>
            </div>
        </div>

        <div class="row points">
            <div class="col-md-4 col-xs-12 must">
                <h2>Must</h2>

                <ul>
                    <li>Retain the original copyright notice shown on the left.</li>
                </ul>
            </div>
            <div class="col-md-4 col-xs-12 can">
                <h2>Can</h2>

                <ul>
                    <li>Use freely for commercial purposes in derivative works.</li>
                    <li>Modify.</li>
                    <li>Distribute.</li>
                    <li>Give partial production/distribution rights to third parties not included in the license.
                    </li>
                    <li>Place a warranty on the software.</li>
                </ul>
            </div>
            <div class="col-md-4 col-xs-12 cannot">
                <h2>Cannot</h2>

                <ul>
                    <li>Use contributors' names, logos or trademarks.</li>
                    <li>Software is released without warranty and the software/license owner cannot be charged for
                        damages.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content license">
        <div class="row">
            <div class="text col-xs-12">
                <h2 id="docs">License of Official and User-contributed Documentation</h2>

                <p>The text contained in the official and user-contributed Yii documentation is
                    licensed to the public under the
                    <a href="https://www.gnu.org/copyleft/fdl.html" rel="nofollow">GNU Free Documentation License
                        (GFDL)</a>.
                </p>

                <p>Permission is granted to copy, distribute and/or modify this document under the terms of the GNU Free
                    Documentation License (GFDL), Version 1.2 or any later version published by the Free Software
                    Foundation; with no Invariant Sections, with no Front-Cover Texts, and with no Back-Cover Texts.</p>
            </div>
        </div>
        <div class="row points">
            <div class="col-md-6 col-xs-12 must">
                <h2>Must</h2>
                <ul>
                    <li>Grant the same freedoms as in original license.</li>
                    <li>Acknowledge the authors in form of linking back to the original article.</li>
                </ul>
            </div>
            <div class="col-md-6 col-xs-12 can">
                <h2>Can</h2>
                <ul>
                    <li>Copy.</li>
                    <li>Modify.</li>
                    <li>Redistribute.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
