/// <reference types="cypress" />

describe('Editace postu', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('navigace na editaci přes detail postu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.url().should('include', '/edit/edit');
  });

  it('formulář je předvyplněný daty postu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.get('input[name*="title"]').should('not.have.value', '');
    cy.get('textarea[name*="content"]').should('not.have.value', '');
  });

  it('úspěšná editace postu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();

    const suffix = ` (upraveno ${Date.now()})`;
    cy.get('input[name*="title"]').type(suffix);
    cy.get('input[type="submit"]').click();

    cy.expectToast('úspěšně');
  });

  it('editace postu – formulář obsahuje upload obrázku', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();
    cy.get('input[name*="postImage"]').should('exist');
  });

  it('editace postu – změna obsahu', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').click();

    const newContent = `Aktualizovaný obsah ${Date.now()}`;
    cy.get('textarea[name*="content"]').clear().type(newContent);
    cy.get('input[type="submit"]').click();

    cy.expectToast('úspěšně');
    // Ověříme, že nový obsah je viditelný na detailu
    cy.contains(newContent).should('exist');
  });

});
