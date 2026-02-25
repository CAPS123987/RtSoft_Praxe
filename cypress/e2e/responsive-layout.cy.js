/// <reference types="cypress" />

describe('Responzivní design a layout', () => {

  it('stránka se načte s dark theme', () => {
    cy.visit('/');
    cy.get('body').should('have.attr', 'data-bs-theme', 'dark');
  });

  it('navigační lišta je sticky nahoře', () => {
    cy.visit('/');
    cy.get('.navbar').should('be.visible');
  });

  it('posty jsou zobrazeny v gridu', () => {
    cy.visit('/');
    cy.get('.grid').should('exist');
    cy.get('.post').should('have.length.greaterThan', 0);
  });

  it('na mobilním rozlišení se stránka správně zobrazí', () => {
    cy.viewport(375, 667); // iPhone SE
    cy.visit('/');
    cy.contains('h1', 'Posty:').should('be.visible');
    cy.get('.post').should('have.length.greaterThan', 0);
  });

  it('na tabletovém rozlišení se stránka správně zobrazí', () => {
    cy.viewport(768, 1024); // iPad
    cy.visit('/');
    cy.contains('h1', 'Posty:').should('be.visible');
  });

  it('footer neexistuje – stránka nemá footer', () => {
    cy.visit('/');
    cy.get('footer').should('not.exist');
  });

  it('sonner toast kontejner existuje na stránce', () => {
    cy.visit('/');
    cy.get('#sonner-toast-container').should('exist');
  });

});
