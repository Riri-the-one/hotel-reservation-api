import { Head, Link } from '@inertiajs/react';

const messages = { 403: 'Accès non autorisé', 404: 'Page introuvable', 409: 'Cette action ne peut plus être effectuée', 500: 'Une erreur est survenue', 503: 'Service temporairement indisponible' };
export default function Error({ status }) {
    return <main className="flex min-h-screen items-center justify-center bg-stone-50 p-6 text-center"><Head title={messages[status]} /><div><p className="text-sm text-amber-800">HôtelRéservation · {status}</p><h1 className="mt-4 text-3xl font-semibold">{messages[status]}</h1><p className="mt-4 text-stone-600">Revenez à vos réservations ou essayez à nouveau plus tard.</p><Link href="/" className="mt-6 inline-block rounded-lg bg-slate-900 px-5 py-3 text-white">Retour à l’accueil</Link></div></main>;
}
