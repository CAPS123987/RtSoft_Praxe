/// <reference types="cypress" />

describe('Vytvoření postu', () => {
  const createdPostIds = [];

  beforeEach(() => {
    cy.login('admin');
  });

  afterEach(function () {
    if (this.createdPostId) {
      createdPostIds.push(this.createdPostId);
    }
  });

  after(() => {
    // Úklid – smažeme všechny vytvořené posty
    if (createdPostIds.length > 0) {
      cy.login('admin');
      createdPostIds.forEach((id) => {
        cy.deleteTestPost(id);
      });
    }
  });

  it('navigace na stránku pro vytvoření postu', () => {
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').click();
    cy.url().should('include', '/edit/create');
  });

  it('úspěšné vytvoření postu', () => {
    const title = `Cypress Test Post ${Date.now()}`;
    cy.createTestPost(title);
  });

  it('nelze vytvořit post bez titulku', () => {
    cy.visit('/edit/create');
    cy.get('textarea[name*="content"]').type('Obsah bez titulku.');
    cy.get('input[type="submit"]').click();
    cy.url().should('include', '/edit/create');
  });

  it('nelze vytvořit post bez obsahu', () => {
    cy.visit('/edit/create');
    cy.get('input[name*="title"]').type('Titulek bez obsahu');
    cy.get('input[type="submit"]').click();
    cy.url().should('include', '/edit/create');
  });

});
