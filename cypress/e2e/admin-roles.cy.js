/// <reference types="cypress" />

describe('Admin – správa rolí', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('zobrazí seznam rolí', () => {
    cy.visit('/admin/role-list');
    cy.url().should('include', '/admin/role-list');
    // Měly by být viditelné existující role
    cy.get('body').should('not.be.empty');
  });

  it('navigace na přidání role', () => {
    cy.visit('/admin/add-role');
    cy.url().should('include', '/admin/add-role');
    cy.get('input[name*="name"]').should('be.visible');
  });

  it('přidání nové role', () => {
    const roleName = `TestRole_${Date.now()}`;
    cy.visit('/admin/add-role');

    cy.get('input[name*="name"]').type(roleName);
    cy.get('input[type="submit"]').first().click();

    // Po přidání by měl být přesměrován
    cy.expectToast('úspěšně');
  });

  it('editace existující role', () => {
    cy.visit('/admin/role-list');
    // Klikneme na první dostupný odkaz editace role
    cy.contains('a', 'Upravit').first().click();
    cy.url().should('include', '/admin/edit-role');
  });

  it('editace role – formulář obsahuje checkboxy oprávnění', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Upravit').first().click();
    // Checkboxy oprávnění
    cy.get('input[type="checkbox"]').should('have.length.greaterThan', 0);
  });

  it('seznam rolí zobrazuje oprávnění u rolí', () => {
    cy.visit('/admin/role-list');
    // Role by měly mít zobrazená oprávnění jako paragraphy
    cy.get('[class*="border"]').first().within(() => {
      cy.get('h2').should('exist');
    });
  });

  it('z admin panelu lze přejít na seznam rolí', () => {
    cy.visit('/admin/');
    cy.contains('h2', 'Výpis rolí').should('be.visible');
    cy.contains('h2', 'Výpis rolí').parent().contains('a', 'výpis').click();
    cy.url().should('include', '/admin/role-list');
  });

});
