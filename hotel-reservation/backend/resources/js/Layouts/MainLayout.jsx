import { Link, usePage } from '@inertiajs/react';

export default function MainLayout({ children }) {
    const { auth, flash } = usePage().props;
    return <div className="min-h-screen flex flex-col bg-stone-50 text-slate-900">
        <a href="#main" className="sr-only focus:not-sr-only">Aller au contenu</a>
        <nav aria-label="Navigation principale" className="border-b border-stone-200 bg-white">
            <div className="mx-auto max-w-7xl px-4 py-5 flex flex-wrap items-center justify-between gap-4">
                <Link href="/" className="font-semibold text-xl">Hôtel<span className="text-amber-700">Réservation</span></Link>
                <div className="flex flex-wrap items-center gap-x-5 gap-y-3 text-sm">
                    <Link href="/">Chambres</Link>
                    {auth?.user ? <>
                        <Link href="/profile/reservations">Mes réservations</Link>
                        <Link href="/profile">Mon compte</Link>
                        {auth.user.is_admin && <Link href="/admin/reservations">Gestion</Link>}
                        <Link href="/logout" method="post" as="button">Déconnexion</Link>
                    </> : <><Link href="/login">Connexion</Link><Link href="/register" className="rounded-lg bg-slate-900 px-4 py-2 text-white">Créer un compte</Link></>}
                </div>
            </div>
        </nav>
        <main id="main" className="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6">
            {flash?.success && <p role="status" className="mb-6 rounded-xl bg-emerald-50 p-4 text-emerald-900">{flash.success}</p>}
            {children}
        </main>
        <footer className="border-t border-stone-200 px-4 py-6 text-center text-sm text-stone-600">Projet de démonstration · Portfolio PFE 2027 · Chambres et tarifs fictifs</footer>
    </div>;
}
