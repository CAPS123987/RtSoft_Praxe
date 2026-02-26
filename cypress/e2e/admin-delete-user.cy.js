/// <reference types="cypress" />

describe('Admin – smazání uživatele (oprávnění deleteUser)', () => {

  const timestamp = Date.now();

  describe('Admin s oprávněním deleteUser', () => {

    it('admin vidí tlačítko "Smazat" v seznamu uživatelů', () => {
      cy.login('admin');
      cy.visit('/admin/user-list');
      cy.contains('a', 'Smazat').should('exist');
    });

    it('admin úspěšně smaže uživatele', () => {
      cy.login('admin');

      // Vytvoříme uživatele ke smazání
      const username = `todelete_${timestamp}`;
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(username);
      cy.get('input[name*="password"]').first().clear().type('heslo123');
      cy.get('select[name*="role"]').first().select(1);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Ověříme, že existuje v seznamu
      cy.visit('/admin/user-list');
      cy.contains(username).should('exist');

      // Smažeme ho
      cy.contains('[class*="border"]', username)
        .contains('a', 'Smazat').click();
      cy.expectToast('smazán');

      // Ověříme, že zmizel ze seznamu
      cy.visit('/admin/user-list');
      cy.contains(username).should('not.exist');
    });

    it('smazání uživatele smaže i jeho příspěvky', () => {
      cy.login('admin');

      // Vytvoříme roli s addPost
      const roleName = `DelTestRole_${timestamp}`;
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Nastavíme oprávnění addPost
      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();
      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      cy.get('input[type="checkbox"][name*="addPost"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele s touto rolí
      const username = `postauthor_${timestamp}`;
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(username);
      cy.get('input[name*="password"]').first().clear().type('heslo123');
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Přihlásíme se jako nový uživatel a vytvoříme post
      const postTitle = `PostToVanish_${timestamp}`;
      cy.loginWith(username, 'heslo123');
      cy.createTestPost(postTitle, 'Tento post zmizí se smazáním uživatele.');

      // Ověříme, že post existuje na homepage
      cy.visit('/');
      cy.contains('.post h2 a', postTitle).should('exist');

      // Přihlásíme se jako admin a smažeme uživatele
      cy.login('admin');
      cy.deleteTestUser(username);

      // Post by měl zmizet z homepage
      cy.visit('/');
      cy.contains('.post h2 a', postTitle).should('not.exist');
    });

    it('admin nemůže smazat sám sebe', () => {
      cy.login('admin');

      // Zjistíme ID admina z URL jeho editace, nebo přímo přes /admin/delete-user
      // a ověříme, že backend odmítne smazání se správnou hláškou
      cy.fixture('users').then((users) => {
        const adminName = users.admin.username;

        cy.visit('/admin/user-list');
        cy.contains('[class*="border"]', adminName).within(() => {
          // Zjistíme ID admina z href editačního odkazu
          cy.get('a[href*="edit-user"]').invoke('attr', 'href').then((href) => {
            const adminId = href.split('/').pop();

            // Pokusíme se smazat admina přímo přes URL (obejdeme confirm dialog)
            cy.visit(`/admin/delete-user/${adminId}`);
            cy.url().should('include', '/admin/user-list');
            cy.expectToast('smazat sám sebe');
          });
        });
      });
    });

  });

  describe('Uživatel BEZ oprávnění deleteUser nemůže mazat uživatele', () => {
    const roleName = `NoDelUserRole_${timestamp}`;
    const userName = `nodeluser_${timestamp}`;
    const userPass = 'test123';

    before(() => {
      cy.login('admin');

      // Vytvoříme roli s adminPanel ale BEZ deleteUser
      cy.visit('/admin/add-role');
      cy.get('input[name*="name"]').type(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      cy.visit('/admin/role-list');
      cy.contains('[class*="border"]', roleName)
        .contains('a', 'Upravit roli').click();

      cy.get('input[type="checkbox"]').each(($cb) => {
        cy.wrap($cb).uncheck({ force: true });
      });
      // Pouze adminPanel – bez deleteUser
      cy.get('input[type="checkbox"][name*="adminPanel"]').check({ force: true });
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');

      // Vytvoříme uživatele
      cy.visit('/admin/');
      cy.get('input[name*="name"]').first().clear().type(userName);
      cy.get('input[name*="password"]').first().clear().type(userPass);
      cy.get('select[name*="role"]').first().select(roleName);
      cy.get('input[type="submit"]').first().click();
      cy.expectToast('úspěšně');
    });

    after(() => {
      // Úklid
      cy.login('admin');
      cy.deleteTestUser(userName);
    });

    it('uživatel bez deleteUser nevidí tlačítko Smazat v seznamu uživatelů', () => {
      cy.loginWith(userName, userPass);
      cy.visit('/admin/user-list');
      cy.contains('a', 'Smazat').should('not.exist');
    });

    it('uživatel bez deleteUser je přesměrován při pokusu o smazání přes URL', () => {
      cy.loginWith(userName, userPass);
      // Zkusíme smazat libovolného uživatele přes URL – zjistíme ID z user-listu
      cy.visit('/admin/user-list');
      cy.get('a[href*="edit-user"]').first().invoke('attr', 'href').then((href) => {
        const userId = href.split('/').pop();
        cy.visit(`/admin/delete-user/${userId}`);
        // Měl by být přesměrován s chybovou hláškou
        cy.url().should('include', '/admin/user-list');
        cy.expectToast('oprávnění');
      });
    });

  });

});

