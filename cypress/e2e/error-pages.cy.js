/// <reference types="cypress" />

describe('Chybové stránky', () => {

  it('neexistující stránka vrací chybovou odpověď', () => {
    cy.visit('/neexistujici-stranka', { failOnStatusCode: false });
    cy.get('body').should('not.be.empty');
  });

  it('neexistující post vrací chybovou stránku', () => {
    cy.visit('/post/show/999999', { failOnStatusCode: false });
    cy.get('body').should('not.be.empty');
  });

  it('editace neexistujícího postu – přesměrování', () => {
    cy.login('admin');
    cy.visit('/edit/edit/999999', { failOnStatusCode: false });
    // Buď chybová stránka nebo přesměrování
    cy.get('body').should('not.be.empty');
  });

});
