<?php
class SecurityFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnPage(['site/security']);
    }

    public function openSecurityPage(\FunctionalTester $I)
    {
        $I->see('Report a Security Issue');
    }

    public function submitEmptyForm(\FunctionalTester $I)
    {
        $I->submitForm('.content form', []);
        $I->expectTo('see validations errors');
        $I->see('Report a Security Issue');
        $I->see('Name cannot be blank');
        $I->see('Email cannot be blank');
        $I->see('Message cannot be blank');
        $I->see('The verification code is incorrect');
    }

    public function submitFormWithIncorrectEmail(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'SecurityForm[name]' => 'tester',
            'SecurityForm[email]' => 'tester.email',
            'SecurityForm[body]' => 'test content',
            'SecurityForm[verifyCode]' => 'testme',
        ]);
        $I->expectTo('see that email address is wrong');
        $I->dontSee('Name cannot be blank', '.help-inline');
        $I->see('Email is not a valid email address.');
        $I->dontSee('Text cannot be blank', '.help-inline');
        $I->dontSee('The verification code is incorrect', '.help-inline');        
    }

    public function submitFormSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'SecurityForm[email]' => 'tester',
            'SecurityForm[name]' => 'tester@example.com',
            'SecurityForm[body]' => 'test content',
            'SecurityForm[verifyCode]' => 'testme',
        ]);
        $I->seeEmailIsSent();
        $I->seeElement('.content form');
        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');        
    }
}
