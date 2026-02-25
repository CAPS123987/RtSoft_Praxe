/// <reference types="cypress" />

describe('Hlavní stránka – seznam postů', () => {

  it('načte hlavní stránku s nadpisem', () => {
    cy.visit('/');
    cy.contains('h1', 'Posty:').should('be.visible');
  });

  it('zobrazí seznam postů', () => {
    cy.visit('/');
    cy.get('.post').should('have.length.greaterThan', 0);
  });

  it('každý post obsahuje titulek a datum', () => {
    cy.visit('/');
    cy.get('.post').first().within(() => {
      cy.get('h2').should('exist');
      cy.get('.date').should('exist');
    });
  });

  it('tlačítko "Vytvořit příspěvek" je viditelné po přihlášení', () => {
    cy.login('admin');
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
  });

  it('tlačítko "Vytvořit příspěvek" není viditelné bez přihlášení', () => {
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').should('not.exist');
  });

  it('klik na titulek postu přesměruje na detail', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.url().should('include', '/post/show');
  });

  it('posty jsou zobrazeny v gridu', () => {
    cy.visit('/');
    cy.get('.grid').should('exist');
  });

  it('post obsahuje zkrácený obsah', () => {
    cy.visit('/');
    cy.get('.post').first().within(() => {
      cy.get('.text-stone-400').should('exist');
    });
  });

  it('nadpis stránky obsahuje "Test project"', () => {
    cy.visit('/');
    cy.title().should('contain', 'Test project');
  });

});
