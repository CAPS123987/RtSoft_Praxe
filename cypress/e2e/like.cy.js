/// <reference types="cypress" />

describe('Like / Unlike postu', () => {
  let testPostId = null;
  const testPostTitle = `LikeTest_${Date.now()}`;

  before(() => {
    // Vytvoříme vlastní testovací post pro like testy
    cy.login('admin');
    cy.createTestPost(testPostTitle, 'Post pro testování like/unlike.');
    cy.get('@createdPostId').then((id) => {
      testPostId = id;
    });
  });

  after(() => {
    // Úklid – smažeme testovací post
    if (testPostId) {
      cy.login('admin');
      cy.deleteTestPost(testPostId);
    }
  });

  it('nepřihlášený uživatel – klik zobrazí toast error', () => {
    cy.visit(`/post/show/${testPostId}`);

    cy.get('#like-btn').should('be.visible');

    cy.get('#like-btn').click();
    cy.expectToast('Pro lajkování se musíte přihlásit');
  });

  it('přihlášený uživatel může likovat post', () => {
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);

    // Zapamatujeme počáteční stav
    cy.get('.like-count').invoke('text').then((countBefore) => {
      const before = parseInt(countBefore, 10);

      cy.get('#like-btn').click();

      // Počkáme na snippet redraw
      cy.get('#like-btn', { timeout: 5000 }).should('exist');
      cy.wait(500);

      cy.get('.like-count').invoke('text').then((countAfter) => {
        const after = parseInt(countAfter, 10);
        // Počet by se měl změnit o 1 (nahoru nebo dolů)
        expect(Math.abs(after - before)).to.eq(1);
      });
    });
  });

  it('přihlášený uživatel může unlikovat post', () => {
    cy.login('admin');
    cy.visit(`/post/show/${testPostId}`);

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
    cy.visit(`/post/show/${testPostId}`);

    // Zjistíme aktuální text ikony
    cy.get('.like-icon').invoke('text').then((iconBefore) => {
      cy.get('#like-btn').click();
      cy.wait(1000);

      cy.get('.like-icon').invoke('text').then((iconAfter) => {
        expect(iconBefore.trim()).to.not.eq(iconAfter.trim());
      });
    });

    // Vrátíme zpět
    cy.get('#like-btn').click();
  });

});
