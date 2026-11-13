import { test, expect } from '@playwright/test';
import fs from 'node:fs';
const screenshots = '../../docs/screenshots';
test('real demo: responsive catalogue, registration, booking and cancellation', async ({ page }) => {
    fs.mkdirSync(screenshots, { recursive: true });
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto('/');
    await expect(page.getByRole('heading', { name: 'Votre prochain séjour commence ici.' })).toBeVisible();
    for (const width of [320, 390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
        await page.screenshot({ path: screenshots + '/home-' + width + '.png', fullPage: true });
    }
    const arrival = new Date(); arrival.setDate(arrival.getDate() + 10);
    const departure = new Date(); departure.setDate(departure.getDate() + 12);
    const date = value => value.toISOString().slice(0, 10);
    await page.getByLabel('Arrivée', { exact: true }).fill(date(arrival));
    await page.getByLabel('Départ', { exact: true }).fill(date(departure));
    await page.getByRole('button', { name: 'Rechercher', exact: true }).click();
    await page.waitForURL(/arrival=/);
    await page.screenshot({ path: screenshots + '/availability.png', fullPage: true });
    await page.goto('/register');
    for (const width of [320, 390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
    await page.getByLabel('Name', { exact: true }).fill('Portfolio Demo');
    await page.getByLabel('Email', { exact: true }).fill('demo-' + Date.now() + '@example.test');
    await page.getByLabel('Password', { exact: true }).fill('Demo-Portfolio-2027!');
    await page.getByLabel('Confirm Password', { exact: true }).fill('Demo-Portfolio-2027!');
    await page.getByRole('button', { name: 'Register', exact: true }).click();
    await page.waitForURL('**/dashboard');
    await page.goto('/chambres/1');
    await page.getByLabel('Arrivée', { exact: true }).fill(date(arrival));
    await page.getByLabel('Départ', { exact: true }).fill(date(departure));
    await page.getByLabel('Voyageurs', { exact: true }).fill('2');
    for (const width of [320, 390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
    await page.screenshot({ path: screenshots + '/booking-form.png', fullPage: true });
    await page.getByRole('button', { name: 'Confirmer la réservation', exact: true }).click();
    await page.waitForURL('**/confirmation');
    await expect(page.getByText('150.00', { exact: false }).first()).toBeVisible();
    for (const width of [320, 390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
    await page.screenshot({ path: screenshots + '/confirmation.png', fullPage: true });
    await page.goto('/profile/reservations');
    for (const width of [320, 390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
    await page.screenshot({ path: screenshots + '/my-reservations.png', fullPage: true });
    page.once('dialog', dialog => dialog.accept());
    await page.getByRole('button', { name: 'Annuler', exact: true }).click();
    await expect(page.getByText('Annulée', { exact: true })).toBeVisible();
    const response = await page.goto('/admin/reservations');
    expect(response.status()).toBe(403);
    expect(errors).toEqual([]);
});
