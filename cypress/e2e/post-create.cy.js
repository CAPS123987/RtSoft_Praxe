/// <reference types="cypress" />

describe('Vytvoření postu', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('navigace na stránku pro vytvoření postu', () => {
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').click();
    cy.url().should('include', '/edit/create');
  });

  it('úspěšné vytvoření postu', () => {
    const title = `Cypress Test Post ${Date.now()}`;
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type(title);
    cy.get('textarea[name*="content"]').type('Toto je testovací obsah vytvořený Cypress testem.');
    cy.get('input[type="submit"]').click();

    // Po vytvoření by měl být přesměrován a vidět flash zprávu
    cy.expectToast('úspěšně');
  });

  it('nelze vytvořit post bez titulku', () => {
    cy.visit('/edit/create');
    cy.get('textarea[name*="content"]').type('Obsah bez titulku.');
    cy.get('input[type="submit"]').click();
    // Měl by zůstat na stránce (validace)
    cy.url().should('include', '/edit/create');
  });

  it('nelze vytvořit post bez obsahu', () => {
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type('Titulek bez obsahu');
    cy.get('input[type="submit"]').click();
    cy.url().should('include', '/edit/create');
  });

});
