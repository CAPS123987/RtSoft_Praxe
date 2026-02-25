/// <reference types="cypress" />

describe('Přidání komentáře', () => {

  it('přihlášený uživatel vidí formulář komentáře', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('input[name*="email"]').should('be.visible');
    cy.get('input[name*="content"]').should('be.visible');
  });

  it('nepřihlášený uživatel nevidí formulář komentáře', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('Pro přidání komentáře se musíte přihlásit').should('be.visible');
  });

  it('úspěšné přidání komentáře', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('input[name*="email"]').type('test@cypress.io');
    cy.get('input[name*="content"]').type(`Cypress komentář ${Date.now()}`);
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();

    cy.expectToast('komentář');
  });

  it('nelze přidat komentář bez emailu', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('input[name*="content"]').type('Komentář bez emailu');
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();

    // Zůstáváme na stránce – validace
    cy.url().should('include', '/post/show');
  });

  it('nelze přidat komentář bez obsahu', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('input[name*="email"]').type('test@cypress.io');
    // Necháme pole content prázdné
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();

    // Zůstáváme na stránce – validace
    cy.url().should('include', '/post/show');
  });

  it('komentář je viditelný i po reloadu stránky', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    const commentText = `Persistent komentář ${Date.now()}`;
    cy.get('input[name*="email"]').type('persist@cypress.io');
    cy.get('input[name*="content"]').type(commentText);
    cy.get('input[type="submit"][value*="komentář"], input[type="submit"]').last().click();

    cy.expectToast('komentář');

    // Reload a ověření, že komentář stále existuje
    cy.reload();
    cy.contains(commentText).should('exist');
  });

});
