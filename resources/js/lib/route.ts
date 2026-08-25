/**
 * Route dictionary matching Laravel routes in web.php
 */
const routes: Record<string, string> = {
    'home': '/',
    'public.rt.profile': '/profil-rt',
    'public.profile.rt': '/profil-rt',
    'public.profile_rt': '/profil-rt',
    'public.kt.profile': '/karang-taruna',
    'public.profile.karang_taruna': '/karang-taruna',
    'public.profile_karang_taruna': '/karang-taruna',
    'public.structure': '/struktur-organisasi',
    'public.announcements.index': '/pengumuman',
    'public.announcements.show': '/pengumuman/{slug}',
    'public.articles.index': '/artikel',
    'public.articles.show': '/artikel/{slug}',
    'public.events.index': '/agenda',
    'public.events.show': '/agenda/{slug}',
    'public.activities.index': '/kegiatan',
    'public.activities.show': '/kegiatan/{slug}',
    'public.gallery': '/galeri',
    'public.gallery.index': '/galeri',
    'public.finance': '/transparansi-keuangan',
    'public.page': '/page/{slug}',

    'login': '/login',
    'login.store': '/login',
    'logout': '/logout',

    'admin.dashboard': '/admin/dashboard',
    'admin.organization.profile': '/admin/organization/profile/{type?}',
    'admin.organization.update': '/admin/organization/{organization}',
    'admin.organization.periods.index': '/admin/organization/periods',
    'admin.organization.periods.store': '/admin/organization/periods',
    'admin.organization.periods.update': '/admin/organization/periods/{period}',
    'admin.organization.periods.destroy': '/admin/organization/periods/{period}',

    'admin.organization.positions.index': '/admin/organization/positions',
    'admin.organization.positions.store': '/admin/organization/positions',
    'admin.organization.positions.update': '/admin/organization/positions/{position}',
    'admin.organization.positions.destroy': '/admin/organization/positions/{position}',

    'admin.organization.members.index': '/admin/organization/members',
    'admin.organization.members.store': '/admin/organization/members',
    'admin.organization.members.update': '/admin/organization/members/{member}',
    'admin.organization.members.destroy': '/admin/organization/members/{member}',
    'admin.organization.members.assign': '/admin/organization/members/{member}/assign-position',
    'admin.organization.members.remove_assignment': '/admin/organization/members/assignments/{assignment}',

    'admin.finance.index': '/admin/finance',
    'admin.finance.transactions.store': '/admin/finance/transactions',
    'admin.finance.transactions.void': '/admin/finance/transactions/{transaction}/void',
    'admin.finance.categories.index': '/admin/finance/categories',
    'admin.finance.categories.store': '/admin/finance/categories',
    'admin.finance.categories.update': '/admin/finance/categories/{category}',
    'admin.finance.categories.destroy': '/admin/finance/categories/{category}',

    'admin.contents.index': '/admin/contents',
    'admin.contents.create': '/admin/contents/create',
    'admin.contents.store': '/admin/contents',
    'admin.contents.edit': '/admin/contents/{content}/edit',
    'admin.contents.update': '/admin/contents/{content}',
    'admin.contents.destroy': '/admin/contents/{content}',
    'admin.contents.editor_media': '/admin/contents/editor-media',
    'admin.contents.upload_gallery': '/admin/contents/{content}/gallery',
    'admin.contents.delete_gallery': '/admin/contents/gallery/{activityMedia}',
    'admin.contents.upload_attachment': '/admin/contents/{content}/attachments',
    'admin.contents.delete_attachment': '/admin/contents/attachments/{attachment}',
    'admin.contents.import_markdown': '/admin/contents/import-markdown',

    'admin.media.index': '/admin/media',
    'admin.media.store': '/admin/media',
    'admin.media.destroy': '/admin/media/{medium}',

    'admin.settings.index': '/admin/settings',
    'admin.settings.update': '/admin/settings',

    'admin.users.index': '/admin/users',
    'admin.users.store': '/admin/users',
    'admin.users.update': '/admin/users/{user}',
    'admin.users.destroy': '/admin/users/{user}',

    'admin.audit_logs.index': '/admin/audit-logs',
};

export interface RouteHelper {
    (name?: string, params?: Record<string, any> | string | number): any;
    current(pattern?: string): boolean;
}

export const route: RouteHelper = function (name?: string, params?: Record<string, any> | string | number): any {
    // If called without arguments: route().current('name')
    if (!name) {
        return {
            current: (pattern?: string) => route.current(pattern),
        };
    }

    let url = routes[name];
    if (!url) {
        return `/${name.replace(/\./g, '/')}`;
    }

    if (params === undefined || params === null) {
        return url.replace(/\/\{[a-zA-Z0-9_]+\?\}/g, '');
    }

    if (typeof params === 'string' || typeof params === 'number') {
        const match = url.match(/\{([a-zA-Z0-9_]+)\??\}/);
        if (match) {
            return url.replace(match[0], encodeURIComponent(String(params)));
        }
        return url;
    }

    // Object params
    const remainingParams = { ...params };

    url = url.replace(/\{([a-zA-Z0-9_]+)(\?)?\}/g, (fullMatch, paramKey, isOptional) => {
        if (paramKey in remainingParams) {
            const val = remainingParams[paramKey];
            delete remainingParams[paramKey];
            return encodeURIComponent(String(val));
        }
        return isOptional ? '' : fullMatch;
    });

    url = url.replace(/\/+$/, '') || '/';

    const queryKeys = Object.keys(remainingParams).filter(
        k => remainingParams[k] !== undefined && remainingParams[k] !== null && remainingParams[k] !== ''
    );
    if (queryKeys.length > 0) {
        const searchParams = new URLSearchParams();
        queryKeys.forEach(k => searchParams.append(k, String(remainingParams[k])));
        url += (url.includes('?') ? '&' : '?') + searchParams.toString();
    }

    return url;
} as RouteHelper;

route.current = function (pattern?: string): boolean {
    if (typeof window === 'undefined') {
        return false;
    }
    const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    if (!pattern) {
        return !!currentPath;
    }

    if (pattern === 'home') {
        return currentPath === '/' || currentPath === '';
    }

    // Wildcard matching e.g. 'public.announcements.*' or 'admin.finance.categories.*'
    if (pattern.endsWith('.*')) {
        const prefix = pattern.slice(0, -2);
        for (const [rName, rUrl] of Object.entries(routes)) {
            if (rName === prefix || rName.startsWith(prefix + '.')) {
                const basePath = rUrl.split('/{')[0].replace(/\/+$/, '');
                if (currentPath === basePath || currentPath.startsWith(basePath + '/')) {
                    return true;
                }
            }
        }
        return false;
    }

    // Exact name match
    const targetUrl = routes[pattern];
    if (targetUrl) {
        const hasParams = targetUrl.includes('{');
        const cleanTarget = targetUrl.split('/{')[0].replace(/\/+$/, '') || '/';

        if (hasParams) {
            return currentPath === cleanTarget || currentPath.startsWith(cleanTarget + '/');
        }
        return currentPath === cleanTarget;
    }

    return false;
};

export default route;
