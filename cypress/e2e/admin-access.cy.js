/// <reference types="cypress" />

describe('Admin panel – přístup', () => {

  it('nepřihlášený uživatel nemá přístup do admin panelu', () => {
    cy.visit('/admin/');
    // Měl by být přesměrován na přihlášení
    cy.url().should('include', '/sign/in');
  });

  it('nepřihlášený uživatel nevidí odkaz Admin v navigaci', () => {
    cy.visit('/');
    cy.contains('a', 'Admin').should('not.exist');
  });

  it('admin vidí odkaz Admin v navigaci', () => {
    cy.login('admin');
    cy.contains('a', 'Admin').should('be.visible');
  });

  it('admin se dostane do admin panelu', () => {
    cy.login('admin');
    cy.contains('a', 'Admin').click();
    cy.contains('Vítejte v administraci!').should('be.visible');
  });

  it('admin panel zobrazuje odkazy na výpisy', () => {
    cy.login('admin');
    cy.visit('/admin/');
    cy.contains('a', 'výpis').should('exist');
    cy.contains('h2', 'Výpis uživatelů').should('be.visible');
    cy.contains('h2', 'Výpis rolí').should('be.visible');
  });

  it('admin panel zobrazuje uvítací zprávu s jménem', () => {
    cy.login('admin');
    cy.visit('/admin/');
    cy.contains('h1', 'Ahoj').should('be.visible');
  });

  it('admin má přístup na seznam uživatelů', () => {
    cy.login('admin');
    cy.visit('/admin/user-list');
    cy.contains('Vítejte v seznamu uživatelů').should('be.visible');
  });

  it('admin má přístup na seznam rolí', () => {
    cy.login('admin');
    cy.visit('/admin/role-list');
    cy.contains('Vítejte v seznamu rolí').should('be.visible');
  });

  it('admin má přístup na přidání role', () => {
    cy.login('admin');
    cy.visit('/admin/add-role');
    cy.contains('Vítejte v přidávání role').should('be.visible');
  });

});
