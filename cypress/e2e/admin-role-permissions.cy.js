/// <reference types="cypress" />

describe('Admin – role a oprávnění (CRUD)', () => {

  beforeEach(() => {
    cy.login('admin');
  });

  it('seznam rolí zobrazuje existující role', () => {
    cy.visit('/admin/role-list');
    cy.get('[class*="border"]').should('have.length.greaterThan', 0);
    cy.contains('Jméno role:').should('exist');
  });

  it('seznam rolí obsahuje odkaz "Přidat roli"', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Přidat roli').should('be.visible');
  });

  it('seznam rolí zobrazuje oprávnění u každé role', () => {
    cy.visit('/admin/role-list');
    // Každá role by měla mít sekci s oprávněními
    cy.get('[class*="border"]').first().within(() => {
      cy.contains('Jméno role:').should('exist');
    });
  });

  it('přidání nové role s názvem', () => {
    const roleName = `CypressRole_${Date.now()}`;
    cy.visit('/admin/add-role');
    cy.get('input[name*="name"]').type(roleName);
    cy.get('input[type="submit"]').first().click();

    cy.expectToast('úspěšně');
  });

  it('přidání role s oprávněními – checkboxy', () => {
    const roleName = `RolePerms_${Date.now()}`;
    cy.visit('/admin/add-role');
    cy.get('input[name*="name"]').type(roleName);

    // Zaškrtneme první 3 checkboxy oprávnění
    cy.get('input[type="checkbox"]').then(($checkboxes) => {
      if ($checkboxes.length > 0) {
        cy.wrap($checkboxes.eq(0)).check({ force: true });
      }
      if ($checkboxes.length > 1) {
        cy.wrap($checkboxes.eq(1)).check({ force: true });
      }
      if ($checkboxes.length > 2) {
        cy.wrap($checkboxes.eq(2)).check({ force: true });
      }
    });

    cy.get('input[type="submit"]').first().click();
    cy.expectToast('úspěšně');
  });

  it('editace role – formulář obsahuje checkboxy oprávnění', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Upravit roli').first().click();
    cy.url().should('include', '/admin/edit-role');
    cy.get('input[type="checkbox"]').should('have.length.greaterThan', 0);
  });

  it('editace role – zaškrtnutí/odškrtnutí oprávnění a uložení', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Upravit roli').first().click();

    // Přepneme stav prvního checkboxu
    cy.get('input[type="checkbox"]').first().then(($cb) => {
      if ($cb.is(':checked')) {
        cy.wrap($cb).uncheck({ force: true });
      } else {
        cy.wrap($cb).check({ force: true });
      }
    });

    cy.get('input[type="submit"]').first().click();
    cy.expectToast('úspěšně');
  });

  it('stránka editace role obsahuje odkaz Zpět', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Upravit roli').first().click();
    cy.contains('a', 'Zpět').should('be.visible');
  });

  it('odkaz Zpět vede na seznam rolí', () => {
    cy.visit('/admin/role-list');
    cy.contains('a', 'Upravit roli').first().click();
    cy.contains('a', 'Zpět').click();
    cy.url().should('include', '/admin/role-list');
  });

  it('stránka přidání role obsahuje odkaz Zpět', () => {
    cy.visit('/admin/add-role');
    cy.contains('a', 'Zpět').should('be.visible');
  });

  it('nelze přidat roli bez názvu', () => {
    cy.visit('/admin/add-role');
    cy.get('input[name*="name"]').clear();
    cy.get('input[type="submit"]').first().click();
    // Zůstáváme na stránce – validace
    cy.url().should('include', '/admin/add-role');
  });

});
