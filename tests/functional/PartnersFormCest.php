<?php
class PartnersFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnPage(['site/partners']);
    }

    public function openPartnersPage(\FunctionalTester $I)
    {
        $I->see('Project Request Form');
    }

    public function submitEmptyForm(\FunctionalTester $I)
    {
        $I->submitForm('.content form', []);
        $I->expectTo('see validations errors');
        $I->see('Project Request Form');
        $I->see('Your Email cannot be blank');
        $I->see('Your Name cannot be blank');
        $I->see('Company cannot be blank');
        $I->see('Project details cannot be blank');
        $I->see('Budget cannot be blank');
        $I->see('When do you want to start? cannot be blank');
        $I->see('The verification code is incorrect');
    }

    public function submitFormWithIncorrectEmail(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'PartnersForm[name]' => 'tester.email',
            'PartnersForm[email]' => 'tester',
            'PartnersForm[company]' => 'Test Company',
            'PartnersForm[body]' => 'test content',
            'PartnersForm[budget]' => '$10,000',
            'PartnersForm[when]' => 'Next month',
            'PartnersForm[verifyCode]' => 'testme',
        ]);
        $I->expectTo('see that email address is wrong');
        $I->dontSee('Your Email cannot be blank', '.help-inline');
        $I->see('Your Email is not a valid email address.');
        $I->dontSee('Your Name cannot be blank', '.help-inline');
        $I->dontSee('Company cannot be blank', '.help-inline');
        $I->dontSee('Project details cannot be blank', '.help-inline');
        $I->dontSee('Budget cannot be blank', '.help-inline');
        $I->dontSee('When do you want to start? cannot be blank', '.help-inline');
        $I->dontSee('The verification code is incorrect', '.help-inline');        
    }

    public function submitFormSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('.content form', [
            'PartnersForm[name]' => 'tester@example.com',
            'PartnersForm[email]' => 'tester',
            'PartnersForm[company]' => 'Test Company',
            'PartnersForm[body]' => 'test content',
            'PartnersForm[budget]' => '$10,000',
            'PartnersForm[when]' => 'Next month',
            'PartnersForm[verifyCode]' => 'testme',
        ]);
        $I->seeEmailIsSent();
        $I->seeElement('.content form');
        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');        
    }
}
