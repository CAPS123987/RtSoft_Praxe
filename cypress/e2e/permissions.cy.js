/// <reference types="cypress" />

describe('Oprávnění a přístupová práva', () => {

  it('nepřihlášený uživatel nevidí admin odkaz', () => {
    cy.visit('/');
    cy.contains('a', 'Admin').should('not.exist');
  });

  it('nepřihlášený uživatel je přesměrován z admin stránek', () => {
    const adminPages = [
      '/admin/',
      '/admin/user-list',
      '/admin/role-list',
      '/admin/add-role',
    ];

    adminPages.forEach((page) => {
      cy.visit(page);
      cy.url().should('include', '/sign/in');
    });
  });

  it('nepřihlášený uživatel je přesměrován ze stránky pro vytvoření postu', () => {
    cy.visit('/edit/create');
    // Buď přesměrován na přihlášení nebo na homepage
    cy.url().should('satisfy', (url) => {
      return url.includes('/sign/in') || url.includes('/homepage');
    });
  });

  it('nepřihlášený uživatel nevidí tlačítko pro vytvoření postu', () => {
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').should('not.exist');
  });

  it('nepřihlášený uživatel nevidí formulář komentáře', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('Pro přidání komentáře se musíte přihlásit').should('be.visible');
    cy.get('input[name*="email"]').should('not.exist');
  });

  it('nepřihlášený uživatel nevidí tlačítka editace/smazání postů', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.contains('a', 'Upravit příspěvek').should('not.exist');
    cy.contains('a', 'Smazat příspěvek').should('not.exist');
  });

  it('nepřihlášený uživatel nevidí tlačítka smazání komentářů', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('.comments').then(($el) => {
      if ($el.find('.comment-content').length > 0) {
        cy.get('.comments .comment-content').each(($comment) => {
          cy.wrap($comment).find('a:contains("Smazat")').should('not.exist');
        });
      }
    });
  });

  it('nepřihlášený uživatel – like tlačítko zobrazí toast o nutnosti přihlášení', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'false');
    cy.get('#like-btn').click();
    cy.expectToast('Pro lajkování se musíte přihlásit');
  });

  it('přihlášený uživatel vidí formulář komentáře', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();
    cy.get('input[name*="email"]').should('be.visible');
    cy.get('input[name*="content"]').should('be.visible');
  });

  it('přihlášený admin vidí tlačítko "Vytvořit příspěvek"', () => {
    cy.login('admin');
    cy.visit('/');
    cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
  });

});
