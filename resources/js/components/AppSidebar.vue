<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    Images,
    Inbox,
    LayoutGrid,
    Newspaper,
    Package,
    Settings,
    Sparkles,
    Tag,
    Tags,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as blogCategoriesIndex } from '@/routes/admin/blog-categories';
import { index as blogsIndex } from '@/routes/admin/blogs';
import { index as brandsIndex } from '@/routes/admin/brands';
import { index as privateLabelIndex } from '@/routes/admin/private-label';
import { index as inquiriesIndex } from '@/routes/admin/private-label/inquiries';
import { index as productsIndex } from '@/routes/admin/products';
import { edit as settingsEdit } from '@/routes/admin/settings';
import { index as slidesIndex } from '@/routes/admin/slides';
import type { NavItem } from '@/types';

const page = usePage();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (page.props.auth.user.role === 'admin') {
        items.push({
            title: 'Slider',
            href: slidesIndex(),
            icon: Images,
        });
        items.push({
            title: 'Brands',
            href: brandsIndex(),
            icon: Tag,
        });
        items.push({
            title: 'Products',
            href: productsIndex(),
            icon: Package,
        });
        items.push({
            title: 'Private Label',
            href: privateLabelIndex(),
            icon: Sparkles,
        });
        items.push({
            title: 'Inquiries',
            href: inquiriesIndex(),
            icon: Inbox,
        });
        items.push({
            title: 'Blog',
            href: blogsIndex(),
            icon: Newspaper,
        });
        items.push({
            title: 'Blog categories',
            href: blogCategoriesIndex(),
            icon: Tags,
        });
        items.push({
            title: 'Settings',
            href: settingsEdit(),
            icon: Settings,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
