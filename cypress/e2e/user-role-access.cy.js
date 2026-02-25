/// <reference types="cypress" />

describe('Přístup na základě uživatelských rolí', () => {

  describe('Běžný uživatel (user)', () => {

    beforeEach(() => {
      cy.login('user');
    });

    it('běžný uživatel se úspěšně přihlásí', () => {
      cy.contains('a', 'Odhlásit').should('be.visible');
    });

    it('běžný uživatel nevidí odkaz Admin v navigaci', () => {
      cy.visit('/');
      cy.contains('a', 'Admin').should('not.exist');
    });

    it('běžný uživatel nemá přístup do admin panelu', () => {
      cy.visit('/admin/');
      // Měl by být přesměrován na přihlášení
      cy.url().should('include', '/sign/in');
    });

    it('běžný uživatel nemá přístup na seznam uživatelů', () => {
      cy.visit('/admin/user-list');
      cy.url().should('include', '/sign/in');
    });

    it('běžný uživatel nemá přístup na seznam rolí', () => {
      cy.visit('/admin/role-list');
      cy.url().should('include', '/sign/in');
    });

    it('běžný uživatel nemá přístup na přidání role', () => {
      cy.visit('/admin/add-role');
      cy.url().should('include', '/sign/in');
    });

    it('běžný uživatel vidí posty na hlavní stránce', () => {
      cy.visit('/');
      cy.get('.post').should('have.length.greaterThan', 0);
    });

    it('běžný uživatel může zobrazit detail postu', () => {
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.url().should('include', '/post/show');
    });

  });

  describe('Editor uživatel', () => {

    beforeEach(() => {
      cy.login('editor');
    });

    it('editor se úspěšně přihlásí', () => {
      cy.contains('a', 'Odhlásit').should('be.visible');
    });

    it('editor nevidí odkaz Admin v navigaci', () => {
      cy.visit('/');
      cy.contains('a', 'Admin').should('not.exist');
    });

    it('editor nemá přístup do admin panelu', () => {
      cy.visit('/admin/');
      cy.url().should('include', '/sign/in');
    });

    it('editor vidí tlačítko "Vytvořit příspěvek"', () => {
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
    });

    it('editor může zobrazit detail postu', () => {
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.url().should('include', '/post/show');
    });

  });

  describe('Admin uživatel', () => {

    beforeEach(() => {
      cy.login('admin');
    });

    it('admin vidí odkaz Admin v navigaci', () => {
      cy.visit('/');
      cy.contains('a', 'Admin').should('be.visible');
    });

    it('admin má přístup do admin panelu', () => {
      cy.visit('/admin/');
      cy.contains('Vítejte v administraci!').should('be.visible');
    });

    it('admin vidí formulář pro přidání uživatele', () => {
      cy.visit('/admin/');
      cy.contains('h2', 'Přidání uživatele').should('be.visible');
    });

    it('admin vidí tlačítko "Vytvořit příspěvek"', () => {
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
    });

    it('admin může přistoupit k editaci role', () => {
      cy.visit('/admin/role-list');
      cy.contains('a', 'Upravit roli').should('exist');
    });

    it('admin vidí tlačítka editace/smazání u postů', () => {
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.get('body').then(($body) => {
        const hasEdit = $body.find('a:contains("Upravit příspěvek")').length > 0;
        const hasDelete = $body.find('a:contains("Smazat příspěvek")').length > 0;
        expect(hasEdit || hasDelete).to.be.true;
      });
    });

  });

  describe('Nepřihlášený uživatel', () => {

    it('je přesměrován z /edit/create na přihlášení', () => {
      cy.visit('/edit/create');
      cy.url().should('satisfy', (url) => {
        return url.includes('/sign/in') || url.includes('/homepage');
      });
    });

    it('nevidí odkaz "Vytvořit příspěvek"', () => {
      cy.visit('/');
      cy.contains('a', 'Vytvořit příspěvek').should('not.exist');
    });

    it('nevidí odkaz Admin', () => {
      cy.visit('/');
      cy.contains('a', 'Admin').should('not.exist');
    });

    it('je přesměrován z admin stránek na přihlášení', () => {
      cy.visit('/admin/');
      cy.url().should('include', '/sign/in');
    });

    it('nevidí formulář komentáře', () => {
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.contains('Pro přidání komentáře se musíte přihlásit').should('be.visible');
    });

    it('nevidí tlačítka editace/smazání postů', () => {
      cy.visit('/');
      cy.get('.post h2 a').first().click();
      cy.contains('a', 'Upravit příspěvek').should('not.exist');
      cy.contains('a', 'Smazat příspěvek').should('not.exist');
    });

  });

});
