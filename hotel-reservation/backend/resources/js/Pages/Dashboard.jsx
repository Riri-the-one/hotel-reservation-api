import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
export default function Dashboard() {
    return <MainLayout><Head title="Mon espace" /><section className="rounded-2xl bg-white p-8"><p className="text-xs uppercase tracking-widest text-amber-800">Bienvenue</p><h1 className="mt-3 text-3xl font-semibold">Préparez votre prochain séjour</h1><div className="mt-8 flex flex-wrap gap-4"><Link href="/" className="rounded-lg bg-slate-900 px-5 py-3 text-white">Explorer les chambres</Link><Link href="/profile/reservations" className="rounded-lg border border-stone-300 px-5 py-3">Mes réservations</Link></div></section></MainLayout>;
}
