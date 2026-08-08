<?php

class SecurityCest
{
    public function openSecurityPage(FunctionalTester $I)
    {
        $I->amOnPage(['site/security']);
        $I->see('Report a Security Issue');
        $I->seeLink('Report a Yii 1.1 vulnerability');
        $I->seeLink('Report a Yii 2 vulnerability');
        $I->seeLink('Find the affected package');
        $I->seeLink('Report a website vulnerability');
        $I->dontSeeElement('.security-report-page form');
    }
}
