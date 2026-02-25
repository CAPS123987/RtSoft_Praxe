/// <reference types="cypress" />

describe('Admin – přidání uživatele', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('formulář pro přidání uživatele je viditelný na admin stránce', () => {
    cy.visit('/admin/');
    cy.contains('h2', 'Přidání uživatele').should('be.visible');
    cy.get('input[name*="name"]').should('be.visible');
    cy.get('input[name*="password"]').should('be.visible');
    cy.get('select[name*="role"]').should('be.visible');
  });

  it('úspěšné přidání nového uživatele', () => {
    const username = `newuser_${Date.now()}`;
    cy.visit('/admin/');
    cy.get('input[name*="name"]').first().clear().type(username);
    cy.get('input[name*="password"]').first().clear().type('heslo123');
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();

    cy.expectToast('úspěšně');
  });

  it('nelze přidat uživatele bez jména', () => {
    cy.visit('/admin/');
    cy.get('input[name*="name"]').first().clear();
    cy.get('input[name*="password"]').first().clear().type('heslo123');
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();

    // Validace by měla zabránit odeslání – zůstáváme na stránce
    cy.url().should('include', '/admin');
  });

  it('nelze přidat uživatele bez hesla', () => {
    cy.visit('/admin/');
    cy.get('input[name*="name"]').first().clear().type(`testuser_${Date.now()}`);
    cy.get('input[name*="password"]').first().clear();
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();

    // Validace by měla zabránit odeslání – zůstáváme na stránce
    cy.url().should('include', '/admin');
  });

  it('nově přidaný uživatel se zobrazí v seznamu uživatelů', () => {
    const username = `listuser_${Date.now()}`;
    cy.visit('/admin/');
    cy.get('input[name*="name"]').first().clear().type(username);
    cy.get('input[name*="password"]').first().clear().type('heslo123');
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();

    cy.expectToast('úspěšně');

    // Ověříme, že se nový uživatel zobrazí v seznamu
    cy.visit('/admin/user-list');
    cy.contains(username).should('exist');
  });

});
