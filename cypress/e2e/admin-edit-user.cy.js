/// <reference types="cypress" />

describe('Admin – editace uživatele', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('navigace na editaci uživatele ze seznamu', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.url().should('include', '/admin/edit-user');
  });

  it('formulář editace je předvyplněný jménem uživatele', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.get('input[name*="name"]').should('not.have.value', '');
  });

  it('formulář editace obsahuje select pro výběr role', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.get('select[name*="role"]').should('exist');
  });

  it('úspěšná editace uživatele – změna jména', () => {
    // Nejprve vytvoříme uživatele k editaci
    const username = `editable_${Date.now()}`;
    cy.visit('/admin/');
    cy.get('input[name*="name"]').first().clear().type(username);
    cy.get('input[name*="password"]').first().clear().type('heslo123');
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();
    cy.expectToast('úspěšně');

    // Najdeme uživatele v seznamu a editujeme ho
    cy.visit('/admin/user-list');
    cy.contains(username).parent().contains('a', 'Upravit').click();

    const newName = `edited_${Date.now()}`;
    cy.get('input[name*="name"]').clear().type(newName);
    cy.get('input[type="submit"]').first().click();

    cy.expectToast('úspěšně');
  });

  it('stránka editace uživatele obsahuje odkaz Zpět', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.contains('a', 'Zpět').should('be.visible');
  });

  it('odkaz Zpět vede zpět na seznam uživatelů', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.contains('a', 'Zpět').click();
    cy.url().should('include', '/admin/user-list');
  });

});
