/// <reference types="cypress" />

describe('Paginace na hlavní stránce', () => {

  const ITEMS_PER_PAGE = 6;

  beforeEach(() => {
    cy.visit('/');
  });

  // ────────────────────────────────────────────────
  // Základní zobrazení
  // ────────────────────────────────────────────────

  it('na první stránce se nezobrazí tlačítko "Předchozí strana"', () => {
    cy.contains('a', 'Předchozí strana').should('not.exist');
  });

  it('na první stránce se zobrazí tlačítko "Další strana"', () => {
    cy.contains('a', 'Další strana').should('be.visible');
  });

  it('zobrazí maximálně 6 postů na stránku', () => {
    cy.get('.post').should('have.length.at.most', ITEMS_PER_PAGE);
  });

  // ────────────────────────────────────────────────
  // Navigace vpřed
  // ────────────────────────────────────────────────

  it('klik na "Další strana" načte další posty', () => {
    // Zapamatujeme si titulky z první stránky
    const firstPageTitles = [];
    cy.get('.post h2').each(($h2) => {
      firstPageTitles.push($h2.text().trim());
    });

    cy.contains('a', 'Další strana').click();

    // Počkáme na AJAX – snippet se překreslí
    cy.get('[id^="snippet-"]').should('exist');

    // Posty by se měly změnit (pokud existuje druhá strana)
    cy.get('.post').then(($posts) => {
      if ($posts.length > 0) {
        const secondPageTitles = [];
        cy.get('.post h2').each(($h2) => {
          secondPageTitles.push($h2.text().trim());
        }).then(() => {
          // Alespoň některé titulky by měly být jiné
          const allSame = secondPageTitles.every((t, i) => t === firstPageTitles[i]);
          if (secondPageTitles.length > 0 && firstPageTitles.length > 0) {
            expect(allSame).to.be.false;
          }
        });
      }
    });
  });

  it('po přechodu na druhou stránku se zobrazí tlačítko "Předchozí strana"', () => {
    cy.contains('a', 'Další strana').click();
    cy.contains('a', 'Předchozí strana').should('be.visible');
  });

  // ────────────────────────────────────────────────
  // Navigace zpět
  // ────────────────────────────────────────────────

  it('klik na "Předchozí strana" vrátí na původní posty', () => {
    // Zapamatujeme si titulky z první stránky
    const firstPageTitles = [];
    cy.get('.post h2').each(($h2) => {
      firstPageTitles.push($h2.text().trim());
    });

    // Přejdeme na druhou stránku
    cy.contains('a', 'Další strana').click();
    cy.contains('a', 'Předchozí strana').should('be.visible');

    // Vrátíme se zpět
    cy.contains('a', 'Předchozí strana').click();

    // Posty by měly být stejné jako na začátku
    cy.get('.post h2').each(($h2, index) => {
      expect($h2.text().trim()).to.equal(firstPageTitles[index]);
    });
  });

  it('po návratu na první stránku zmizí tlačítko "Předchozí strana"', () => {
    cy.contains('a', 'Další strana').click();
    cy.contains('a', 'Předchozí strana').should('be.visible').click();
    cy.contains('a', 'Předchozí strana').should('not.exist');
  });

  // ────────────────────────────────────────────────
  // URL parametr page
  // ────────────────────────────────────────────────

  it('persistentní parametr page se aktualizuje v URL po kliknutí na "Další strana"', () => {
    cy.contains('a', 'Další strana').click();
    // Naja by měla aktualizovat URL s page=2
    cy.url().should('include', 'page=2');
  });

  it('přímý přístup na ?page=2 zobrazí druhou stránku postů', () => {
    // Zapamatujeme titulky první stránky
    const firstPageTitles = [];
    cy.get('.post h2').each(($h2) => {
      firstPageTitles.push($h2.text().trim());
    });

    // Navštívíme přímo druhou stránku
    cy.visit('/?page=2');

    cy.get('.post').then(($posts) => {
      if ($posts.length > 0) {
        const secondPageTitles = [];
        cy.get('.post h2').each(($h2) => {
          secondPageTitles.push($h2.text().trim());
        }).then(() => {
          if (secondPageTitles.length > 0 && firstPageTitles.length > 0) {
            const allSame = secondPageTitles.every((t, i) => t === firstPageTitles[i]);
            expect(allSame).to.be.false;
          }
        });
      }
    });
  });

  it('přímý přístup na ?page=2 zobrazí tlačítko "Předchozí strana"', () => {
    cy.visit('/?page=2');
    cy.contains('a', 'Předchozí strana').should('be.visible');
  });

  it('přímý přístup na ?page=1 nezobrazí tlačítko "Předchozí strana"', () => {
    cy.visit('/?page=1');
    cy.contains('a', 'Předchozí strana').should('not.exist');
  });

  // ────────────────────────────────────────────────
  // Vícenásobné stránkování
  // ────────────────────────────────────────────────

  it('dvakrát klik na "Další strana" zvýší page na 3', () => {
    cy.contains('a', 'Další strana').click();
    cy.url().should('include', 'page=2');

    cy.contains('a', 'Další strana').click();
    cy.url().should('include', 'page=3');
  });

  it('vpřed a zpět vícekrát se vrátí na stránku 1', () => {
    // 1 → 2 → 3 → 2 → 1
    cy.contains('a', 'Další strana').click();
    cy.contains('a', 'Další strana').click();
    cy.contains('a', 'Předchozí strana').click();
    cy.contains('a', 'Předchozí strana').click();

    cy.contains('a', 'Předchozí strana').should('not.exist');
  });

  // ────────────────────────────────────────────────
  // Edge cases
  // ────────────────────────────────────────────────

  it('stránka bez page parametru zobrazí první stránku (výchozí page=1)', () => {
    cy.visit('/');
    cy.get('.post').should('have.length.greaterThan', 0);
    cy.contains('a', 'Předchozí strana').should('not.exist');
  });

  it('prázdná stránka (vysoké číslo) nezobrazí žádné posty', () => {
    cy.visit('/?page=9999');
    cy.get('.post').should('have.length', 0);
  });

  it('po přechodu na prázdnou stránku se dá vrátit zpět', () => {
    cy.visit('/?page=9999');
    cy.get('.post').should('have.length', 0);
    cy.contains('a', 'Předchozí strana').should('be.visible').click();
    // Po kliknutí zpět by se page mělo snížit
    cy.url().should('include', 'page=9998');
  });

  // ────────────────────────────────────────────────
  // AJAX chování – snippety
  // ────────────────────────────────────────────────

  it('stránkování přes AJAX neprovede full page reload', () => {
    // Přidáme marker do DOMu
    cy.window().then((win) => {
      win.document.body.setAttribute('data-cypress-marker', 'loaded');
    });

    cy.contains('a', 'Další strana').click();

    // Pokud se stránka nepřenačetla, marker stále existuje
    cy.get('body[data-cypress-marker="loaded"]').should('exist');
  });

  it('po AJAX stránkování zůstává navigační panel viditelný', () => {
    cy.contains('a', 'Další strana').click();
    cy.get('.navbar').should('be.visible');
  });

  it('po AJAX stránkování zůstává nadpis "Posty:" viditelný', () => {
    cy.contains('a', 'Další strana').click();
    cy.contains('h1', 'Posty:').should('be.visible');
  });

  // ────────────────────────────────────────────────
  // Paginace s přihlášeným uživatelem
  // ────────────────────────────────────────────────

  it('stránkování funguje i po přihlášení', () => {
    cy.login('admin');
    cy.visit('/');

    cy.contains('a', 'Další strana').click();

    cy.get('.post').should('exist');
    cy.contains('a', 'Předchozí strana').should('be.visible');
  });

  it('tlačítko "Vytvořit příspěvek" zůstává viditelné po stránkování (přihlášený admin)', () => {
    cy.login('admin');
    cy.visit('/');

    cy.contains('a', 'Vytvořit příspěvek').should('be.visible');

    cy.contains('a', 'Další strana').click();

    // Tlačítko by mělo zůstat viditelné – je mimo snippet
    cy.contains('a', 'Vytvořit příspěvek').should('be.visible');
  });

  // ────────────────────────────────────────────────
  // Vytvoření dostatku postů pro paginaci a cleanup
  // ────────────────────────────────────────────────

  describe('paginace s dynamicky vytvořenými posty', () => {
    const TEST_PREFIX = 'CyPagTest';
    const POSTS_TO_CREATE = 8; // vytvoříme víc než 6 aby byla 2. strana

    before(() => {
      cy.login('admin');

      // Vytvoříme posty pro test
      for (let i = 1; i <= POSTS_TO_CREATE; i++) {
        cy.createTestPost(`${TEST_PREFIX} ${i}`, `Obsah paginačního testu číslo ${i}.`);
      }

      cy.logout();
    });

    after(() => {
      // Cleanup – smažeme vytvořené posty
      cy.login('admin');
      for (let i = 1; i <= POSTS_TO_CREATE; i++) {
        cy.deleteTestPostByTitle(`${TEST_PREFIX} ${i}`);
      }
    });

    it('s dostatkem postů existuje druhá stránka s posty', () => {
      cy.visit('/');
      cy.get('.post').should('have.length', ITEMS_PER_PAGE);

      cy.contains('a', 'Další strana').click();
      cy.get('.post').should('have.length.greaterThan', 0);
    });

    it('první stránka má přesně 6 postů, když jich existuje více', () => {
      cy.visit('/');
      cy.get('.post').should('have.length', ITEMS_PER_PAGE);
    });

    it('posty na druhé stránce jsou jiné než na první', () => {
      cy.visit('/');
      const firstPageTitles = [];
      cy.get('.post h2').each(($h2) => {
        firstPageTitles.push($h2.text().trim());
      });

      cy.contains('a', 'Další strana').click();

      cy.get('.post h2').each(($h2) => {
        const title = $h2.text().trim();
        expect(firstPageTitles).to.not.include(title);
      });
    });
  });
});

