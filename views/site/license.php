<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'License';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container style_external_links">
    <div class="row">
        <div class="content site-license">
            <h2 id="framework">License of Yii Framework</h2>

            <div class="row">
                <div class="col-xs-6 legal">
                    <p>The Yii framework is free software. It is released under the terms of the following BSD License.</p>

                    <p>Copyright &copy; 2008-<?php echo date('Y'); ?> by <a href="http://www.yiisoft.com">Yii Software LLC</a><br/>All rights reserved.</p>

                    <p>Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:</p>

                    <ul>
                        <li>Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer. </li>
                        <li>Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.</li>
                        <li>Neither the name of Yii Software LLC nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.</li>
                    </ul>

                    <p>THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</p>
                </div>

                <div class="col-xs-6 simple">
                    <h2 class="must">Must</h2>

                    <ul>
                        <li>Retain the original copyright notice shown on the left.</li>
                    </ul>

                    <h2 class="can">Can</h2>

                    <ul>
                        <li>Use freely for commercial purposes in derivative works.</li>
                        <li>Modify.</li>
                        <li>Distribute.</li>
                        <li>Give partial production/distribution rights to third parties not included in the license.</li>
                        <li>Place a warranty on the software.</li>
                    </ul>

                    <h2 class="cannot">Cannot</h2>

                    <ul>
                        <li>Use contributors' names, logos or trademarks.</li>
                        <li>Software is released without warranty and the software/license owner cannot be charged for damages.</li>
                    </ul>
                </div>
            </div>

            <h2 id="docs">License of Official and User-contributed Documentation</h2>

            <div class="row">
                <div class="col-xs-6 legal">
                    <p>The text contained in the official and user-contributed Yii documentation is
                        licensed to the public under the
                        <a href="http://www.gnu.org/copyleft/fdl.html" rel="nofollow">GNU Free Documentation License (GFDL)</a>.
                    </p>

                    <p>Permission is granted to copy, distribute and/or modify this document under the terms of the GNU Free Documentation License (GFDL), Version 1.2 or any later version published by the Free Software Foundation; with no Invariant Sections, with no Front-Cover Texts, and with no Back-Cover Texts.</p>
                </div>

                <div class="col-xs-6 simple">
                    <h2 class="must">Must</h2>
                    <ul>
                        <li>Grant the same freedoms as in original license.</li>
                        <li>Acknowledge the authors in form of linking back to the original article.</li>
                    </ul>

                    <h2 class="can">Can</h2>
                    <ul>
                        <li>Copy.</li>
                        <li>Modify.</li>
                        <li>Redistribute.</li>
                    </ul>
                </div>
            </div>

            <?php /*
            <h2 id="3rd-party">3rd Party Licenses</h2>

            <div class="row">
                <div class="col-xs-6 legal">
                    <p>The Yii project site uses a subset of icons which are part of the <a href="http://p.yusukekamiyamane.com/" target="_blank" rel="nofollow">Fugue Icons</a> set created by Yusuke Kamiyamane. The icon set is licenced under the <a href="http://creativecommons.org/licenses/by/3.0/" target="_blank">Creative Commons Attribution 3.0 License</a>.</p>
                </div>

                <div class="col-xs-6 simple">
                    <h2 class="must">Must</h2>
                    <ul>
                        <li>Retain the original copyright and license.</li>
                    </ul>

                    <h2 class="can">Can</h2>
                    <ul>
                        <li>Use for commercial purposes in derivative works.</li>
                        <li>Modify.</li>
                        <li>Distribute.</li>
                    </ul>

                    <h2 class="cannot">Cannot</h2>

                    <ul>
                        <li>Place a warranty on the software.</li>
                        <li>Software is released without warranty and the software/license owner cannot be charged for damages.</li>
                    </ul>
                </div>
            </div> */ ?>
        </div>
    </div>
</div>
