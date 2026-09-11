import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        comments: []
    },

    mutations: {
        setComments(state, comments) {
            state.comments = comments
        },

        addComment(state, comment) {
            state.comments.push(comment)
        },

        removeComment(state, id) {
            state.comments = state.comments.filter(comment => comment.id !== id)
        }
    },

    actions: {
        async fetchComments({ commit }) {
            const response = await axios.get('/api/comments')

            commit('setComments', response.data)
        },

        async addComment({ commit }, comment) {
            const response = await axios.post('/api/comments', comment)

            commit('addComment', response.data)

            return response.data
        },

        async deleteComment({ commit }, id) {
            await axios.delete(`/api/comments/${id}`)

            commit('removeComment', id)
        }
    }
})