/// <reference types="cypress" />

describe('Admin – správa uživatelů', () => {
  const createdUsers = [];

  beforeEach(() => {
    cy.login('admin');
  });

  after(() => {
    // Úklid – smažeme všechny vytvořené uživatele
    if (createdUsers.length > 0) {
      cy.login('admin');
      createdUsers.forEach((username) => {
        cy.deleteTestUser(username);
      });
    }
  });

  it('zobrazí seznam uživatelů', () => {
    cy.visit('/admin/user-list');
    cy.contains('Vítejte v seznamu uživatelů').should('be.visible');
    cy.get('[class*="border"]').should('have.length.greaterThan', 0);
  });

  it('seznam uživatelů obsahuje jméno a roli', () => {
    cy.visit('/admin/user-list');
    cy.contains('Jméno:').should('exist');
    cy.contains('Role:').should('exist');
  });

  it('odkaz "Upravit" vede na editaci uživatele', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.url().should('include', '/admin/edit-user');
  });

  it('editace uživatele – formulář je předvyplněný', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Upravit').first().click();
    cy.get('input[name*="name"]').should('not.have.value', '');
  });

  it('přidání uživatele přes admin panel', () => {
    cy.visit('/admin/');
    const username = `testuser_${Date.now()}`;
    createdUsers.push(username);

    cy.get('input[name*="name"]').first().clear().type(username);
    cy.get('input[name*="password"]').first().clear().type('testpassword123');
    // Vybrat roli (select)
    cy.get('select[name*="role"]').first().select(1);
    cy.get('input[type="submit"]').first().click();

    cy.expectToast('úspěšně');
  });

  it('z admin panelu lze přejít na seznam uživatelů', () => {
    cy.visit('/admin/');
    cy.contains('h2', 'Výpis uživatelů').should('be.visible');
    cy.contains('h2', 'Výpis uživatelů').parent().contains('a', 'výpis').click();
    cy.url().should('include', '/admin/user-list');
  });

  it('stránka se seznamem uživatelů obsahuje odkaz Zpět', () => {
    cy.visit('/admin/user-list');
    cy.contains('a', 'Zpět').should('be.visible');
  });

  it('stránka se seznamem uživatelů zobrazuje uvítací zprávu', () => {
    cy.visit('/admin/user-list');
    cy.contains('h1', 'Ahoj').should('be.visible');
  });

});
