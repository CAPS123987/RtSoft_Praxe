/// <reference types="cypress" />

describe('Like / Unlike postu', () => {

  it('nepřihlášený uživatel – klik zobrazí toast error', () => {
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('#like-btn').should('be.visible');
    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'false');

    cy.get('#like-btn').click();
    cy.expectToast('Pro lajkování se musíte přihlásit');
  });

  it('přihlášený uživatel může likovat post', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('#like-btn').should('have.attr', 'data-logged-in', 'true');

    // Zapamatujeme počáteční stav
    cy.get('.like-count').invoke('text').then((countBefore) => {
      const before = parseInt(countBefore, 10);

      cy.get('#like-btn').click();

      // Počkáme na AJAX odpověď
      cy.wait(1000);

      cy.get('.like-count').invoke('text').then((countAfter) => {
        const after = parseInt(countAfter, 10);
        // Počet by se měl změnit o 1 (nahoru nebo dolů)
        expect(Math.abs(after - before)).to.eq(1);
      });
    });
  });

  it('přihlášený uživatel může unlikovat post', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    // Klikneme dvakrát – like a pak unlike
    cy.get('#like-btn').click();
    cy.wait(1000);

    cy.get('.like-count').invoke('text').then((countAfterLike) => {
      cy.get('#like-btn').click();
      cy.wait(1000);

      cy.get('.like-count').invoke('text').then((countAfterUnlike) => {
        const afterLike = parseInt(countAfterLike, 10);
        const afterUnlike = parseInt(countAfterUnlike, 10);
        expect(Math.abs(afterLike - afterUnlike)).to.eq(1);
      });
    });
  });

  it('ikona srdce se mění podle stavu', () => {
    cy.login('admin');
    cy.visit('/');
    cy.get('.post h2 a').first().click();

    cy.get('#like-btn').invoke('attr', 'data-liked').then((likedBefore) => {
      cy.get('#like-btn').click();
      cy.wait(1000);

      cy.get('#like-btn').invoke('attr', 'data-liked').then((likedAfter) => {
        expect(likedBefore).to.not.eq(likedAfter);
      });
    });

    // Vrátíme zpět
    cy.get('#like-btn').click();
  });

});

