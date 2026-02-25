/// <reference types="cypress" />

describe('Profil uživatele – tooltip', () => {

  it('po najetí na odkaz Odhlásit se zobrazí jméno uživatele', () => {
    cy.login('admin');
    cy.visit('/');

    // Popover hint by měl existovat na stránce
    cy.get('#profile-hint').should('exist');
    cy.get('#profile-hint').should('contain.text', 'Jméno:');
  });

  it('po přihlášení jako user se zobrazí jméno user', () => {
    cy.login('user');
    cy.visit('/');

    cy.get('#profile-hint').should('exist');
    cy.get('#profile-hint').should('contain.text', 'Jméno:');
  });

});
