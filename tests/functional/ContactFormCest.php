<?php
class ContactFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnPage(['site/contact']);
    }

    public function openContactPage(\FunctionalTester $I)
    {
        $I->see('Contact Us');
    }

    public function submitEmptyForm(\FunctionalTester $I)
    {
        $I->submitForm('.content form', []);
        $I->expectTo('see validations errors');
        $I->see('Contact Us');
        $I->see('Name cannot be blank');
        $I->see('Email cannot be blank');
        $I->see('Subject cannot be blank');
        $I->see('Message cannot be blank');
        $I->see('The verification code is incorrect');
    }

    public function submitFormWithIncorrectEmail(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester.email',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
            'ContactForm[verifyCode]' => 'testme',
        ]);
        $I->expectTo('see that email address is wrong');
        $I->dontSee('Name cannot be blank', '.help-inline');
        $I->see('Email is not a valid email address.');
        $I->dontSee('Subject cannot be blank', '.help-inline');
        $I->dontSee('Body cannot be blank', '.help-inline');
        $I->dontSee('The verification code is incorrect', '.help-inline');        
    }

    public function submitFormSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'ContactForm[email]' => 'tester',
            'ContactForm[name]' => 'tester@example.com',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
            'ContactForm[verifyCode]' => 'testme',
        ]);
        $I->seeEmailIsSent();
        $I->dontSeeElement('.content form');
        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');        
    }
}
