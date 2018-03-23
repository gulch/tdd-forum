<script>
    import Favorite from './Favorite.vue';
    export default {
        props: ['attributes'],
        component: { Favorite },
        data() {
            return {
                editing: false,
                body: this.attributes.body
            }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.attributes.id, {
                    body: this.body
                });

                this.editing = false;

                $(this.$el).fadeOut(() => {
                    flash('Updated!');
                });
            },
            destroy() {
                axios.delete('/replies/' + this.attributes.id);

                flash('Reply has been deleted.');
            }
        }
    }
</script>