<?php
/* @var $this yii\web\View */
?>
<div class="site-index">
<!-- ==========================
JUMBOTRON - START
=========================== -->
<div class="jumbotron jumbotron-index">
    <div class="container">

        <h1><img src="/image/yii_petals.svg" alt="Yii Framework" width="80" />yii<span class="hero-framework">framework</span></h1>

        <h2>The solid foundation for your PHP application.</h2>

        <div class="row features">
            <div class="col-md-10 col-md-offset-2">
                <ul class="fa-ul">
                  <li><i class="fa-li fa fa-rocket fa-inverse fa-2x"></i><strong>Fast</strong>  Yii gives you maximum functionality by adding the least possible overhead.</li>
                  <li><i class="fa-li fa fa-lock fa-inverse fa-2x"></i><strong>Secure</strong>  Sane defaults and built in tools help you write solid and secure code.</li>
                  <li><i class="fa-li fa fa-clock-o fa-inverse fa-2x"></i><strong>Efficient</strong>  Write more code in less time with simple yet powerful APIs and code generation.</li>
                  <li><i class="fa-li fa fa-asterisk fa-inverse fa-2x"></i><strong>Flexible</strong>  It works out of the box using reasonable defaults but is highly adjustable to fit your needs.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- end of jumbotron -->
<div class="yii-feature-wrapper">
    <!-- start of features -->
    <div class="yii-features">
        <div class="container">
        	<div class="row equalizer">
            	<div class="col-xs-12 col-sm-6 col-lg-4">
        			<div class="feature-box blue">
        				<div class="icon">
        					<div class="image"><i class="fa fa-database"></i></div>
        					<div class="info">
        						<h3 class="title">Relational and noSQL databases</h3>
                      <p class="watch">Yii has all you need to work with both relational and noSQL databases. Both powerful ActiveRecord and convenient query builder are there to help.</p>
        						<div class="more">
        							<a href="#" title="Title Link">
        								Read More <i class="fa fa-angle-double-right"></i>
        							</a>
        						</div>
        					</div>
        				</div>
        				<div class="space"></div>
        			</div>
        		</div>

                <div class="col-xs-12 col-sm-6 col-lg-4">
        			<div class="feature-box yellow">
        				<div class="icon">
        					<div class="image"><i class="fa fa-wrench"></i></div>
                  <div class="info">
                    <h3 class="title">Great tools</h3>
                      <p class="watch">Debugger will help you tracking errors, analyzing performance and various events of the application.
                      Gii is there to generate code for you. An error page will show you enough details to fix error in no time.</p>
        						<div class="more">
        							<a href="#" title="Title Link">
        								Read More <i class="fa fa-angle-double-right"></i>
        							</a>
        						</div>
        					</div>
        				</div>
        				<div class="space"></div>
        			</div>
        		</div>

                <div class="col-xs-12 col-sm-6 col-lg-4">
        			<div class="feature-box green">
        				<div class="icon">
        					<div class="image"><i class="fa fa-thumbs-up"></i></div>
                  <div class="info">
                    <h3 class="title">Exceptional community</h3>
                    <p class="watch">Yii has one of the most helpful communities out there. There are forums, IRC chat, active GitHub
                    development, collaborative wiki and much more.</p>
        						<div class="more">
        							<a href="#" title="Title Link">
        								Read More <i class="fa fa-angle-double-right"></i>
        							</a>
        						</div>
        					</div>
        				</div>
        				<div class="space"></div>
        			</div>
        		</div>
        	</div>
        </div>
    </div>
    <!-- end of features -->
    <!-- start of poweredby -->
    <section class="content-separator section-poweredby">
       <div class="container">
       	<div class="row">
               <div class="col-md-3">
                   <div class="poweredby">
                       <div class="title"><img src="<?= Yii::getAlias('@web/image/facebook.png') ?>" alt="Facebook"/></div>
                   </div>
               </div>
               <div class="col-md-3">
                   <div class="poweredby">
                       <div class="title"><img src="<?= Yii::getAlias('@web/image/stay.png') ?>" alt="Stay.com"/></div>
                   </div>
               </div>
               <div class="col-md-3">
                   <div class="poweredby">
                       <div class="title"><img src="<?= Yii::getAlias('@web/image/vice.png') ?>" alt="VICE"/></div>
                   </div>
               </div>
               <div class="col-md-3">
                   <div class="poweredby">
                       <div class="title"><img src="<?= Yii::getAlias('@web/image/fifa.png') ?>" alt="FIFA"/></div>
                   </div>
               </div>
           </div>
       </div>
    </section>
    <!-- end of poweredby -->
    <!-- start of testimonials -->
    <section class="section-testimonials">
        <div class="container">
        	<div class="row">
                <div class="col-md-12">
                    <div id="testimonials-slider">
                        <div class="item">
                            <i class="fa fa-quote-left fa-pull-left"></i>
                            <p>Choosing the right PHP framework was a vital decision when we set out to build Craft. With its elegant, modular architecture, rich internationalization support, and helpful documentation, Yii was a perfect fit.</p>
                            <h3>Brandon Kelly, <small>Craft</small></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end of testimonials -->
</div>
</div> <!-- class site-index -->
